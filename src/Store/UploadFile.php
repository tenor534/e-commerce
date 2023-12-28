<?php
class UploadFile  {
    
    private $droits = 0755;
    private $poidMax = 2;
    private $type = array();
    private $path;
    private $prefixeName;
    private $name;
	private $extension;
	
	private $maxWidth;
	private $maxHeight;
	
	private $errorUpload;
    
    //SET
	public function setDroits($v) {
		$this->droits = $v;
	}
	public function setPoidMax($v) {
		$this->poidMax = $v;
	}
    public function setType($v) {
        $this->type = explode("/", $v);
    }
    public function setPath($v) {
        $this->path = $v;
    }
	public function setPrefixeName($v) {
		$this->prefixeName = $v;
	}
    public function setName($v) {
        $this->name = preg_replace('`[^a-zA-Z0-9]+`i', "-", $v);
    }
	public function setMaxDimensions($v, $v2) {
		$this->maxWidth = $v;
		$this->maxHeight = $v2;
	}
    
    //GET
	public function getPath() {
        return $this->path;
    }
	public function getPrefixeName() {
        return $this->prefixeName;
    }
    public function getName() {
        return $this->prefixeName.$this->name;
    }
	public function getExtension() {
		return ".".$this->extension;
	}
	public function getPathFileName() {
		return $this->path."/".$this->prefixeName.$this->name.".".$this->extension;
	}
	public function getErrorUpload() {
		return $this->errorUpload;
	}
	
	//CONSTRUCT
	public function __construct($postFile) {
		$this->postFile = $postFile;
	}
    
	//METHOD
    public function upload() {
		if($this->postFile != "") {
			if($this->controls()) {
				if($this->createDir()) {
					$this->uniqName();
					if($this->moveFile()) {
						$this->errorUpload = "<b class=''>SUCCES</b> : Le fichier a bien &eacute;t&eacute; t&eacute;l&eacute;charg&eacute;";
						return true;
					}
				}
			}
		}
		else $this->errorUpload = "<b class=''>ERREUR</b> : La valeur du champs FILE n'a pas &eacute;t&eacute; parametr&eacute;e";
    }
	private function controls() {
		if($this->setErrorUpload())
			if($this->verifSize())
				if($this->verifExtension())
					if($this->verifImage())
						return true;
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
			$this->errorUpload = "<b class=''>ERREUR</b> : Aucune destination n'a &eacute;t&eacute; sp&eacute;cifi&eacute;e";
			return false;
		}
    }
	private function setErrorUpload() {
		switch ($_FILES[$this->postFile]['error']) {
			case 1:
				$this->errorUpload = "<b class=''>ERREUR</b> : Le fichier est trop volumineux pour la configuration du serveur (upload_max_filesize).";
				break;
			case 2:
				$this->errorUpload = "<b class=''>ERREUR</b> : Le fichier est trop volumineux pour la configuration du serveur (max_file_size).";
				break;
			case 3:
				$this->errorUpload = "<b class=''>ERREUR</b> : Le fichier n'a &eacute;t&eacute; que partiellement t&eacute;l&eacute;charg&eacute;.";
				break;
			case 4:
				$this->errorUpload = "<b class=''>ERREUR</b> : Aucun fichier n'a &eacute;t&eacute; t&eacute;l&eacute;charg&eacute;.";
				break;
			case 6:
				$this->errorUpload = "<b class=''>ERREUR</b> : Un dossier temporaire est manquant.";
				break;
			case 7:
				$this->errorUpload = "<b class=''>ERREUR</b> : &Eacute;chec de l'&eacute;criture du fichier sur le disque.";
				break;
		}
		if($this->errorUpload == "") return true;
	}
	private function verifSize() {
		if(filesize($_FILES[$this->postFile]['tmp_name']) > ($this->poidMax*1024*1024) ) {
			$this->errorUpload = "<b class=''>ERREUR</b> : Le fichier est trop volumineux";
			return false;
		}
		return true;
	}
	private function verifExtension() {
		if(preg_match('`^(.+)\.(.+)+$`i', $_FILES[$this->postFile]['name'], $extension)) {
			$this->extension = strtolower($extension[2]);
			if(is_array($this->type) && !in_array($this->extension, $this->type) ) {
				$this->errorUpload = "<b class=''>ERREUR</b> : Le fichier n'est pas au format demand&eacute;";
				return false;
			}
			else {
				if(!$this->verifMimeType($extension[2])) {
					$this->errorUpload = "<b class=''>ERREUR</b> : Le fichier semble &ecirc;tre corrompu";
					return false;
				}
			}
			return true;
		}
		return false;
	}
	private function verifMimeType($extension) {
		$mimeType = $_FILES[$this->postFile]['type'];
		switch($extension) {
			//IMG
			case "jpg": case "jpeg": case "jpe": case "JPG": case "JPEG": case "JPE":
				if($mimeType == "image/jpeg" || $mimeType == "image/pjpeg") return true; break;
			case "gif": case "gif":
				if($mimeType == "image/gif" || $mimeType == "image/pgif") return true; break;
			case "png": case "PNG":
				if($mimeType == "image/png" || $mimeType == "image/x-png") return true; break;
			//DOC
			case "doc": case "dot":
				if($mimeType == "application/msword") return true; break;
			case "xls": case "xla":
				if($mimeType == "application/msexcel" || $mimeType == "application/vnd.ms-excel") return true; break;
			case "pdf": 
				if($mimeType == "application/pdf") return true; break;
			case "csv": 
				if($mimeType == "text/comma-separated-values") return true; break;
			case "txt": 
				if($mimeType == "text/plain") return true; break;
			case "rft": 
				if($mimeType == "text/richtext") return true; break;
			//ARCHIVE
			case "gz": 
				if($mimeType == "application/gzip") return true; break;
			case "zip": 
				if($mimeType == "application/zip") return true; break;
			//AUDIO
			case "mp2": case "mp3":
				if($mimeType == "audio/x-mpeg") return true; break;
			case "waw":
				if($mimeType == "audio/x-waw") return true; break;
			//VIDEO
			case "mpeg": case "mpg": case "mpe":
				if($mimeType == "video/mpeg") return true; break;
			case "qt": case "ov":
				if($mimeType == "video/quicktime") return true; break;
			case "avi":
				if($mimeType == "video/x-msvideo") return true; break;
			case "movie":
				if($mimeType == "video/x-sgi-movie") return true; break;
		}
		return false;
	}
	private function verifImage() {
		if(preg_match('`^(jpg|jpeg|png|gif)$`', $this->extension)) {
			$img = getimagesize($_FILES[$this->postFile]['tmp_name']);
			if($this->maxWidth != "" || $this->maxHeight != "") {
				if($img[0] > $this->maxWidth || $img[1] > $this->maxHeight) {
					$this->errorUpload = "<b class=''>ERREUR</b> : L'image d&eacute;passe les dimensions autoris&eacute;es";
					return false;
				}
			}
		}
		return true;
	}
	private function uniqName() {
		if($this->name == "") {
			$this->name = $this->prefixeName.uniqid("", true);
		}
	}
	private function moveFile() {
		if(!move_uploaded_file($_FILES[$this->postFile]['tmp_name'], $this->path."/".$this->prefixeName.$this->name.".".$this->extension)) {
			$this->errorUpload = "<b class=''>ERREUR</b> : Le transfert du fichier a &eacute;chou&eacute;";
			return false;
		}
		return true;
	}
}
/* if(isset($_FILES['file'])) {
    $upload = new UploadFile("file");               //Instanciation
    $upload->setPostFile("file");             //Nom du INPUT FILE (name)
    $upload->setType("jpg");                  //Extension(s) autorisee(s) - string, separer par / les differentes extensions ex:"doc/gif/jpeg"
    $upload->setPath("UPLOAD/1");             //Chemin d'upload - les retours avec "../" sont acceptes
    $upload->upload();
	echo $upload->getErrorUpload();
	
	
	<!-- <form method="post" enctype="multipart/form-data">
    <input type="file" name="file" />
    <input type="submit" value="upload" />
</form> -->

} */
?>
