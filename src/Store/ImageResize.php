<?php
class ImageResize {

	private $memoryUsed = false;
	private $infosDebug;
	
	private $droits = 0777;
	private $memoryLimit = 32;
	
	private $pathSource;
	private $imgNameSource;
	private $imgSource;
	private $extension;
	private $sizeSource;
	private $typeResize;
	
	private $ressource;
	
	private $forceDimensions = false;
	private $deleteSource = false;
	private $alphaMode = false;
	private $txtImg;
	private $fontFamily = 'polices/arial.ttf';
	
	private $errorResize;
	
	//SET
	public function setMemoryUsed() {
		$this->memoryUsed = true;
	}
	public function setDroits($v) {
		$this->droits = $v;
	}
	public function setnbrResize($v) {
		$this->nbrResize = $v;
	}
	public function setMemoryLimit($v) {
		$this->memoryLimit = $v;
	}
	public function setTxtImg($v) {
		$this->txtImg = $v;
	}
	public function setForceDimensions() {
		$this->forceDimensions = true;
	}
	public function setDeleteSource() {
		$this->deleteSource = true;
	}
	public function setAlphaMode() {
		$this->alphaMode = true;
	}
	
	//GET
	private function getMemory() {
		if($this->extension == ".png" || $this->extension == ".PNG") 
			$this->memory = 0;
		else {
			$this->infosDebug = getimagesize($this->imgSource);
			$m_need = round((($this->infosDebug[0]*$this->infosDebug[1]*$this->infosDebug['bits']*$this->infosDebug['channels']/8+pow(2,16))*1.65)*$this->nbrResize); 
			$this->memory = round($m_need / pow(1024,2),2);
		}
	}
	public function getImgSource() {
		return $this->imgSource;
	}
	public function getImgName() {
		return $this->imgName;
	}
	public function getErrorResize() {
		return $this->errorResize;
	}
	public function getPathImg() {
		return $this->pathImg;
	}
	
	//CONSTRUCT
	public function __construct($pathSource, $imgName, $extension, $nbrResize=1) {
		if(file_exists($pathSource."/".$imgName.$extension)) {
			$this->pathSource = $pathSource;
			$this->imgName = $imgName;
			$this->extension = $extension;
			$this->imgNameSource = $imgName.$extension;
			$this->imgSource = $pathSource."/".$this->imgNameSource;
			$this->nbrResize = $nbrResize;
			
			$this->getMemory();
			if($this->memory >= $this->memoryLimit) {
				$newSource = new ImageResize($this->pathSource, $this->imgName, $this->extension);
				$this->ressource = $newSource->resize("homothety", $this->pathSource, "1000", substr($this->imgNameSource,0,-4));
				$this->sizeSource = getimagesize($newSource->imgSource);
				imagedestroy($newSource->ressource);
			}			
			else {
				if(!$this->imgWorking())
					$this->errorResize = "<b class=''>ERREUR</b> : L'image de travail n'a pas pu &ecirc;tre cr&eacute;&eacute;e";
			}
		}
		else {
			$this->errorResize = "<b class=''>ERREUR</b> : L'image source n'existe pas";
			return false;
		}
	}
	
	//METHOD
	private function imgWorking() {
		$this->sizeSource = getimagesize($this->imgSource);
		switch($this->sizeSource[2]) {
			case 1: // gif
				$this->ressource = @imagecreatefromgif($this->imgSource); break;
			case 2: // jpeg
				$this->ressource = @imagecreatefromjpeg($this->imgSource); break;
			case 3: // png
				$this->ressource = @imagecreatefrompng($this->imgSource); break;
			default:
				$this->errorResize = "<b class=''>ERREUR</b> : L'image source n'est pas reconnue"; 
				return false;
		}
		return true;
	}
	public function resize($typeResize, $path, $dimensions="", $imgName="") {
		if(is_resource($this->ressource)) {
			$this->typeResize = $typeResize;
			$this->path = $path;
			$this->uniqName($imgName);
			
			switch($this->typeResize) {
				case "homothety":
					$finalImg = $this->defineHomothety($dimensions); break;
				case "homothetyHeight":
					$finalImg = $this->defineHomothety($dimensions, "height"); break;
				case "crop":
					$finalImg = $this->defineCrop($dimensions); break;
				case "wallpaper":
					$finalImg = $this->defineWallpaper(); break;
			}
			if($this->createDir())
				if($this->createImg($finalImg)) {
					$this->getMemory();
					$this->pathImg = $this->path."/".$this->imgName.$this->extension;
					return $finalImg;
				}
		}
		else {
			$this->errorResize = "<b class=''>ERREUR</b> : La ressource de l'image n'est pas accessible"; 
			return false;
		}
	}		
	private function defineHomothety($dimensions, $constraint="") {
		if(preg_match('`\/`', $dimensions)) {
			$tabDimensions = explode("/", $dimensions);
			
			//paysage
			if($this->sizeSource[0] > $this->sizeSource[1]) {
				$width = round($tabDimensions[0]);
				$height = round($this->sizeSource[1]*($tabDimensions[0]/$this->sizeSource[0]));
			}
			//portrait
			else {
				$width = round($this->sizeSource[0]*($tabDimensions[1]/$this->sizeSource[1]));
				$height = round($tabDimensions[1]);
			}
		}
		else {
			if($constraint == "height") {
				$height = round($dimensions);
				$width = round($this->sizeSource[0]*($height/$this->sizeSource[1]));
			}
			else {
				$width = round($dimensions);
				$height = round($this->sizeSource[1]*($width/$this->sizeSource[0]));
			}
		}
		if(!$this->forceDimensions && ($this->sizeSource[0] < $width && $this->sizeSource[1] < $height)) {
			$width = round($this->sizeSource[0]);
			$height = round($this->sizeSource[1]);
		}
		
		$finalImg = imagecreatetruecolor($width, $height);
		if($this->alphaMode) 
			$this->alpha($finalImg);

		imagecopyresampled($finalImg, $this->ressource, 0, 0, 0, 0, $width, $height, $this->sizeSource[0], $this->sizeSource[1]);
		return $finalImg;
	}
	private function defineCrop($dimensions) {
		if(preg_match('`\/`', $dimensions)) {
			$tabDimensions = explode("/", $dimensions);
			$width = $tabDimensions[0];
			$height = $tabDimensions[1];
		}
		else {
			$width = $dimensions;
			$height = $dimensions;
		}
		if(!$this->forceDimensions && ($this->sizeSource[0] < $width || $this->sizeSource[1] < $height)) {
			$width = $this->sizeSource[0];
			$height = $this->sizeSource[1];
		}
		//RAPPORT
		$rapportWidth = $this->sizeSource[0]/$width;
		$rapportHeight = $this->sizeSource[1]/$height;
		$rapportScale = ($rapportWidth < $rapportHeight)?1/$rapportWidth:1/$rapportHeight;
		
		$ajustWidth = round($this->sizeSource[0]*$rapportScale);
		$ajustHeight = round($this->sizeSource[1]*$rapportScale);
			
		//IMG intermediaire
		$ajustImg = imagecreatetruecolor($ajustWidth, $ajustHeight);
		if($this->alphaMode) 
			$this->alpha($ajustImg);
			
		imagecopyresampled($ajustImg, $this->ressource, 0, 0, 0, 0, $ajustWidth, $ajustHeight, $this->sizeSource[0], $this->sizeSource[1]);
		
		//COORDONNEES
		$coordWidthSource = 0;
		$coordHeightSource = 0;
		if($this->sizeSource[0] > $this->sizeSource[1])
			$coordWidthSource = round(($ajustWidth - $width) / 2);
		else 
			$coordHeightSource = round(($ajustHeight - $height) / 2);
			
		$finalImg = imagecreatetruecolor($width, $height);
		if($this->alphaMode) 
			$this->alpha($finalImg);
			
		imagecopy($finalImg, $ajustImg, 0, 0, $coordWidthSource, $coordHeightSource, $ajustWidth, $ajustHeight);
		return $finalImg;
	}
	private function defineWallpaper() {
		return $this->ressource;
	}
	private function createImg($finalImg) {
		$this->writeTxtImg($finalImg);
		if($this->alphaMode) 
			$this->alpha($finalImg);
		
		switch($this->sizeSource[2]) {
			case 1: // gif
				$op = imagegif($finalImg, $this->path."/".$this->imgName.$this->extension, 100); break;
			case 2: // jpeg
				$op = imagejpeg($finalImg, $this->path."/".$this->imgName.$this->extension, 100); break;
			case 3: // png
				$op = imagepng($finalImg, $this->path."/".$this->imgName.$this->extension); break;
			default:
				$this->errorResize = "<b class=''>ERREUR</b> : L'image ".$this->imgName." n'a pas pu &ecirc;tre redimention&eacute;e"; 
				return false; break;
		}
		return $op;
	}
	private function alpha($img) {
		imagealphablending($img, false);
		imagesavealpha($img, true);
	}
	private function writeTxtImg($img) {
		if($this->txtImg != "" && $this->typeResize == "wallpaper") {
			$shadow = imagecolorallocate($this->ressource, 128, 128, 128);
			$blanc = imagecolorallocate($this->ressource, 223, 223, 223);
			$police = 20;
			putenv('GDFONTPATH='.realpath('.'));
			
			$font = $this->fontFamily;			
			
			imagettftext($img, $police, 0, 25, 40, $shadow, $font, $this->txtImg);
			imagettftext($img, $police, 0, 26, 41, $blanc, $font, $this->txtImg);
		}
	}
	private function uniqName($imgName) {
		if($imgName == "")
			$imgName = uniqid("", true);
		$this->imgName = $imgName;
	}
	private function createDir() {
		if($this->path != "") {
			if(preg_match('`\/`', $this->path)) {
				$path = explode("/", $this->path);
				$newDir = "";
				foreach($path as $dir) {
					if($dir == "..") {
						$newDir .= "../";
						continue;
					}
					if(!file_exists($newDir.$dir))
						mkdir($newDir.$dir, $this->droits); 
						
					$newDir .= $dir."/";
				}
			}
			else if(!file_exists($this->path))
				mkdir($this->path, $this->droits);
				
			return true;
		}
		else {
			$this->errorResize = "<b class=''>ERREUR</b> : Aucune destination n'a &eacute;t&eacute; sp&eacute;cifi&eacute;e";
			return false;
		}
    }
	public function deleteFile($file) {
		if(file_exists($file)) {
			@unlink($file);
			return true;
		}
		else
			return false;
	}
	public function __destruct() {			
		if($this->deleteSource) {
			if(!$this->deleteFile($this->imgSource))
				$this->errorResize = "<b class=''>ERREUR</b> : Impossible de supprimer l'image source";
		}
		if($this->memoryUsed)
			echo $this->errorResize = "<br /><b class=''>DEBUG</b> : ".$this->memory." Mo de cache utilis&eacute;e";
		
		if(is_resource($this->ressource))
			imagedestroy($this->ressource);	
	}
}
?>