<?php


namespace OCA\ocDashboard;

/*
 * super class for all widgets
 * generel methods
 * 
 * @author Florian Steffens
 * 
 */
use OC_DB;
use OC_L10N;
use OC_Log;
use OCP\App;
use OCP\DB;
use OCP\User;
use OCP\Util;

abstract class Widget {

    const STATUS_NOTHING    = 0;
    const STATUS_OKAY       = 1;
    const STATUS_NEW        = 2;
    const STATUS_PROBLEM    = 3;
    const STATUS_ERROR      = 4;
    const STATUS_CUSTOM     = 5;

    protected $id = "";
	protected $name = "";
	protected $l;
	protected $user;
	protected $conf;
	protected $status;
	protected $errorMsg;
	protected $interval;
	protected $icon;
	protected $link;
	protected $cond;
	protected $scripts;
	protected $styles;

    protected $_helper;
	
	function __construct($widgetConf) {
		$this->id = $widgetConf['id'];
		$this->name = $widgetConf['name'];
		$this->l = OC_L10N::get('ocDashboard');
		$this->user = User::getUser();
		$this->conf = json_decode($widgetConf['conf'], true);
		$this->status = 0;
		$this->errorMsg = "";
		$this->htmlHash = "";
		$this->html = "";
		$this->interval = $widgetConf['refresh']*1000; // in seconds
		$this->icon = $widgetConf['icon'];
		$this->link = $widgetConf['link'];
		$this->cond = $widgetConf['cond'];
		$this->scripts = $widgetConf['scripts'];
		$this->styles = $widgetConf['styles'];

        //print_r(Array(OC_App::getAppPath('ocDashboard')."/l10n/widgets/".$this->id."/".OC_L10N::findLanguage().".php"));
        //$this->l->load("");

        $this->_helper = new helper();
	}


// --- PUBLIC ----------------------------------------

    abstract function getWidgetData();

	/*
	 * @return returns all data for the actual widget
	 */
	public function getData() {
		if($this->checkConditions()) {
			$return = $this->getWidgetData();
			if($this->errorMsg != "") {
				$return = Array("error"=>$this->errorMsg);
				$this->status = $this::STATUS_ERROR;
			} else {
				$this->loadScripts();
				$this->loadStyles();
			}
		} else {
			$return = Array("error"=>"Missing required app.");
			$this->status = $this::STATUS_ERROR;
		}
				
		$return['id'] = $this->id;
		$return['status'] = $this->getStatus($return);
		$return['interval'] = $this->interval;
		$return['icon'] = $this->icon;
		$return['link'] = $this->link;
		$return['name'] = $this->name;

        return $return;
	}
		
// --- PROTECTED --------------------------------------

// --- PRIVATE ----------------------------------------
	
	/*
	 * loads all script that are defined in the config Array
	 */
	private function loadScripts() {
		if(isset($this->scripts) && $this->scripts != "") {
			foreach (explode(",", $this->scripts) as $script) {
                if($script != "") {
    				Util::addscript('ocDashboard', 'widgets/'.$this->id.'/'.$script);
                }
			}
		}
	}
	
	
	/*
	 * loads all styles that are defined in the config Array
	 */
	private function loadStyles() {
		if(isset($this->styles) && $this->styles != "") {
			foreach (explode(",", $this->styles) as $style) {
                if($style != "") {
    				Util::addStyle('ocDashboard', 'widgets/'.$this->id.'/'.$style);
                }
            }
		}
	}
	
	
	/*
	 * set hash to DB
	 * set and return status local
	 * 
	 * @param $data data for hash in method setHashAndStatus
	 * 
	 * @return status number
	 * see constants
	 */
	private function getStatus($data) {
		$this->cleanHashs();
		$this->setHashAndStatus($data);
        if( $this->status < $this::STATUS_PROBLEM && $this->errorMsg != '' ) {
            $this->status = $this::STATUS_PROBLEM;
        }
		return $this->status;
	}
	
	
	/*
	 * delete all hashs older than 24 hours
	 */
	private function cleanHashs() {
		$sql = 'DELETE FROM `*PREFIX*ocDashboard_usedHashs` WHERE `user` = ? AND `timestamp` < ?;';
		$query = DB::prepare($sql);
		$params = Array($this->user, time()-60*60*24);
		$result = $query->execute($params);
			
		if (DB::isError($result)) {
			Util::writeLog('ocDashboard',"Could not clean hashs.", Util::WARN);
			Util::writeLog('ocDashboard', OC_DB::getErrorMessage($result), OC_Log::ERROR);
		}
	}
	
	
	/*
	 * set status (is the hash new? => status = 2)
	 * writes Hash in DB, next time we know if it was used or it is new
	 * 
	 * @param $data data for hash
	 */
	private function setHashAndStatus($data) {
		$hash = sha1(json_encode($data));

		// hash exists in DB ?
		$sql = 'SELECT * FROM `*PREFIX*ocDashboard_usedHashs` WHERE usedHash = ? AND widget = ? AND user = ? LIMIT 1;';
		$params = Array($hash,$this->id,$this->user);
		$query = DB::prepare($sql);

        $all = $query->execute($params)->fetchAll();
        //var_dump($all);
        $resultNum = count($all);

        // if not in DB, write to DB
		if( $resultNum == 0 ) {
			$sql2 = 'INSERT INTO `*PREFIX*ocDashboard_usedHashs` (usedHash,widget,user,timestamp) VALUES (?,?,?,?); ';
			$params = Array($hash,$this->id,$this->user,time());
			$query2 = DB::prepare($sql2);
			$result2 = $query2->execute($params);
			if (DB::isError($result2)) {
				Util::writeLog('ocDashboard',"Could not write hash to db.", Util::WARN);
				Util::writeLog('ocDashboard', OC_DB::getErrorMessage($result2), OC_Log::ERROR);
			}
            $this->status = $this::STATUS_NEW;
		} else {
            $this->status = $this::STATUS_NOTHING;
        }
	}
	
	
	/*
	 * @param $field name of fild
	 * @return default value for field from conf array
	 */
	protected function getDefaultValue ($field) {
		foreach ($this->conf as $conf) {
			if($conf['id'] == $field) {
				if(isset($conf['options']) && isset($conf['default'])) {
					foreach ($conf['options'] as $option) {
						if($option['id'] == $conf['default']) {
							return $option['id'];
						}
					}
				} elseif(isset($conf['default'])) {
					return $conf['default'];
				}
			}
		}
		return null;
	}

	
	/*
	 * @param $widget widget name
 	 * @return true if all conditon apps are availible
	 */
	private function checkConditions() {
		if(isset($this->cond) && $this->cond != "") {
			foreach(explode(",",$this->cond) as $cond) {
				if(App::isEnabled($cond) != 1) {
					Util::writeLog('ocDashboard',"App ".$cond." missing for ".$this->name, Util::WARN);
					return false;
				}
			}
		}
		return true;
	}


    /*
     * clean escaped characters
     *
     * @param string input
     * @return clean string output
     */
    protected function cleanSpecialCharacter($str) {
        //$str = str_replace('\\', '#=#', $str);
        $str = str_replace('\r', '<br>', $str);
        $str = str_replace('\n', '<br>', $str);
        $str = str_replace('\,', ',', $str);
        //$str = str_replace('#=#', '&#92;', $str);
        return $str;
    }

}
