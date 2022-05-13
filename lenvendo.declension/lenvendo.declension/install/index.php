<?
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\IO\File;


Loc::loadMessages(__FILE__);

class lenvendo_declension extends CModule
{
	var $MODULE_ID = 'lenvendo.declension';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;

	public function __construct()
	{
	   if(file_exists(__DIR__."/version.php")){
	
			$arModuleVersion = [];
            		include __DIR__ . '/version.php';

			$this->MODULE_ID 		   = str_replace("_", ".", get_class($this));
    		$this->MODULE_VERSION 	   = $arModuleVersion["VERSION"];
    		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
    
    		$this->MODULE_NAME =  Loc::getMessage('LENVENDO_DECLENSION_NAME');
    		$this->MODULE_DESCRIPTION =  Loc::getMessage('LENVENDO_DECLENSION_MODULE_DESCRIPTION');
    		$this->PARTNER_NAME =  Loc::getMessage("LENVENDO_DECLENSION_PARTNER_NAME");
    		$this->PARTNER_URI = Loc::getMessage("LENVENDO_DECLENSION_PARTNER_URI");
		}        
        return false;
	}

	public function DoInstall()
	{
		global $APPLICATION;
	
		if(CheckVersion(ModuleManager::getVersion("main"), "14.00.00")){
	
			$this->InstallFiles();	
			$this->InstallDB();
			ModuleManager::registerModule($this->MODULE_ID);	
			$this->InstallEvents();
            
		}else{
	
			$APPLICATION->ThrowException(Loc::getMessage("LENVENDO_DECLENSION_ERROR_VERSION"));
		}
        
       	$APPLICATION->IncludeAdminFile(
			Loc::getMessage("LENVENDO_DECLENSION_INSTALL_TITLE")." \"".Loc::getMessage("LENVENDO_DECLENSION_NAME")."\"",
			__DIR__."/step.php"
		);
	
		return false;
	}
    
    public function InstallFiles(){
        
		return false;
	}

    public function InstallDB(){
        
		return false;
	}
    
    public function InstallEvents(){

		return false;
	}
    
    public function DoUninstall(){
	
        global $APPLICATION;
        
		$this->UnInstallFiles();
		$this->UnInstallEvents();
		$this->UnInstallDB();	
		ModuleManager::unRegisterModule($this->MODULE_ID);
        
		$APPLICATION->IncludeAdminFile(
			Loc::getMessage("LENVENDO_DECLENSION_UNINSTALL_TITLE")." \"".Loc::getMessage("LENVENDO_DECLENSION_NAME")."\"",
			__DIR__."/unstep.php"
		);        
	
		return false;
	}

	public function UnInstallFiles(){
        
		return false;
	}

    public function UnInstallDB(){
        
		return false;
	}

	public function UnInstallEvents(){

		return false;
	}
}
?>