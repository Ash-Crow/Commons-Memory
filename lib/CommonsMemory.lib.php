<?php

class CommonsMemory {
	/// Creates a Memory game from a given Wikimedia Commons category
	
	///
	/// Properties
	///

	/**
	 * Themes list
	 * @var array
	 */
	public $themes_list = array(
              13893548  =>  "Animals",
              13893806  =>  "↳ Birds",
              13894568  =>  "↳ Mammals",
              14630751  =>  "Architecture",
              20022386  =>  "Astronomy",
              14630825  =>  "Landscapes",
              30597927  =>  "Paintings"
            );

	/**
	 * The chosen theme
	 * @var int
	 */
	public $theme;

	/**
	 * The number of pictures to retrieve from Wikimedia Commons
	 * @var int
	 */	
	public $items_number;

	/**
	 * The pictures
	 * @var array
	 */	
	public $items_array;


	/**
	 * The number of cards
	 * @var int
	 */	
	public $cards_number;

	///
	/// Functions
	///

	/**
	 * Initializes a new instance of the CommonsMemory class
	 *
	 * @param int $theme The chosen theme
	 * @param int $number The number of pairs.
	 */
	function __construct ($theme,$number=10) {
		$this->theme = $theme;
		$this->items_number = $number;
		$this->cards_number = $number * 2;

		$this->items_array = array();
	}

	/**
	 * Sets the basic board
	 *
	 */
	public function setBoard() {
		echo '<div id="the-board" class="row">';
		for ($i=0; $i < $this->cards_number ; $i++) {
			echo '
			<div class="col-xs-6 col-md-3">
				<a href="#" class="thumbnail nailthumb-container square-thumb" >
					<img src="img/back.png" class="hidden-card" id="pic'.$i.'">
				</a>
			</div>';
        }
        echo '</div>';
	}

	/**
	 * Lists the images
	 *
	 */
	public function imageList() {
		echo '<h3>Images used in this game:</h3>';
		echo '<a href="#" id="toggleImageList">[Show/Hide]</a>';
		echo '<div id="imagelist" style="display: none">';
		foreach ($this->items_array as $key => $value) {
			echo '
			<div class="media">
  				<a class="media-left media-middle" href="'.$value['descriptionurl'].'">
    				<img src="'.$value['thumburl'].'" width=80px alt="'.$value['canonicaltitle'].'">
  				</a>
  				<div class="media-body">
    				<h4 class="media-heading">'.$value['canonicaltitle'].'</h4>
    				<p>Uploaded by '.$value['user'].'. See the <a href="'.$value['descriptionurl'].'">description page</a> on Wikimedia Commons.</p>
  				</div>
			</div>';
          }
        echo '</div>';
	}

	/**
	 * Lists the themes for the menu
	 *
	 */
	public function listThemes(){
		foreach ($this->themes_list as $key => $value) {
			echo '<a href="index.php?theme='. $key .'" class="list-group-item';
			if ($this->theme == $key){ echo ' active';}
			echo '">'.$value.'</a>'; 
          }
	}

	/**
	 * Retrieve the the pictures from Wikimedia Commons
	 */
	public function run() {
		$url_base			=	'https://commons.wikimedia.org';
		$query_base			= 	'/w/api.php?action=query&prop=imageinfo&format=json&iiprop=canonicaltitle%7Curl%7Cuser%7Cmime%7Cthumbmime%7Cmediatype&iiurlheight=250&rawcontinue=';
		$query_generator	=	'&generator=categorymembers&gcmtype=file&gcmlimit=50&&gcmpageid='.$this->theme;
		$url 				= $url_base.$query_base.$query_generator;

		$data = json_decode(file_get_contents($url), true);

		if (isset($data['query']['pages'])) {
			$images = $data['query']['pages'];
			foreach ($images as $id => $value) {
				$imageinfo = $value['imageinfo'][0];

				// We keep only bitmap images with a ratio close enough to a square picture.
				if (($imageinfo['mediatype'] == 'BITMAP' ) && ($imageinfo['thumbwidth'] >= 150) && ($imageinfo['thumbwidth'] <= 450)) {	
					$this->items_array[$id]['canonicaltitle']	=	$imageinfo['canonicaltitle'];
					$this->items_array[$id]['thumburl']			=	$imageinfo['thumburl'];
					$this->items_array[$id]['descriptionurl']	=	$imageinfo['descriptionurl'];
					$this->items_array[$id]['user']				=	$imageinfo['user'];
				}
			}

			if (count($this->items_array) >= $this->items_number) {
				// Shuffle and truncate the list to the number of cards we want
				shuffle($this->items_array);
				array_splice($this->items_array, $this->items_number);

				$this->setBoard();
			} else {
				$this->alert ("There are not enough pictures in this category.",$class="danger");
			}
		} else {
			$this->alert ("No pictures found",$class="danger");
		}
		

		/*
		//**DEBUG**
		echo '<pre>';
		print_r($this->items_array);
		echo '</pre>'; */
	}


	/**
	 * Prints a message
	 */
	public function alert ($message,$class="info") {
		switch ($class) {
			case 'success':
				$header = "Success!";
				break;
			case 'warning':
				$header = "Warning:";
				break;
			case 'danger':
				$header = "Error:";
				break;
			default:
				$class = "info";
				$header = "Info:";
				break;
		}

		echo '<div class="alert alert-'.$class.'" role="alert"><strong>'.$header.'</strong> '.$message.'</div>';
	}

	public function jsOutput() {
		$output_list = "var items = [";
		foreach ($this->items_array as $key => $value) {
			$output_list.= '"'.$value['thumburl'].'", '."\n";
		}
		$output_list = substr($output_list, 0, -3);
		$output_list.="];";

		echo $output_list."\n";		
	}
	
}