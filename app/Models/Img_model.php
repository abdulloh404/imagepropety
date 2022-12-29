<?php

//
//

namespace App\Models;

use App\Models\Db_model;

use App\Models\Auth_model;

class Img_model{
	
 
	
	//
	//
	public function genImg( $srcFile = '69495834_1040178656183505_180537657502203904_n.jpg', $saveFile = 'aaaaaaaaaaa.png', $frameWidth = 500, $frameHeight = 500, $autoAlign = true, $left = 0, $top = 0, $resize = true, $option = array() ) {
		
		$option = convertObJectToArray( $option );
		$option['border'] = isset( $option['border'] )? $option['border']: 0;
		$option['padding'] = isset( $option['padding'] )? $option['padding']: 0;
		$option['rotate'] = isset( $option['rotate'] )? $option['rotate']: 0;
		$option['crop_frame'] = isset( $option['crop_frame'] )? $option['crop_frame']: true;
		$option['showExample'] = isset( $option['showExample'] )? $option['showExample']: false;
		$option['cutImg'] = isset( $option['cutImg'] )? convertObJectToArray( $option['cutImg'] ): array();
		
		if( empty( $option['saveFile'] ) ) {
			
			$option['saveFile'] = $saveFile;
		}
		
		$this->load( $srcFile );
		
		$this->rotate( $option['rotate'] );
		
		$thumb = $this->image;
		
		if( $resize == false ) {
			
			$frameWidth = imagesx( $thumb );
			
			$frameHeight = imagesy( $thumb );
		}
		
		if( imagesx( $thumb ) >= imagesy( $thumb ) ) {
			
			$newThumbHeight = $frameWidth / imagesx( $thumb ) * imagesy( $thumb );

			$newThumbWidht = $frameWidth;
		}
		else {
			if( $frameWidth >= $frameHeight ) {

				$newThumbHeight = $frameHeight;

				$newThumbWidht = $frameHeight / imagesy( $thumb ) * imagesx( $thumb );
			}
			else {

				$newThumbHeight = $frameWidth / imagesx( $thumb ) * imagesy( $thumb );

				$newThumbWidht = $frameWidth;
			}
		}
		
		if( !empty( $option['crop_frame'] ) ) {

			$frameWidth = $newThumbWidht;
			
			$frameHeight = $newThumbHeight;
		}

		$frameWidth += ( $option['border'] * 2 );

		$frameHeight += ( $option['border'] * 2 );

		$newThumbWidht -= ( $option['padding'] * 2 );

		$newThumbHeight -= ( $option['padding'] * 2 );

		if( $autoAlign == true ) {

			$distX = ( $frameWidth - $newThumbWidht ) / 2;
			$distY = ( $frameHeight - $newThumbHeight ) / 2;
		}
		else {

			$distX = $left;
			$distY = $top;
		}
		
		if( !empty( $option['cutImg'] ) ) {
			
			
			$distX = $left * -1;
			$distY = $top * -1;
			
			$frameWidth = $option['cutImg']['w'];

			$frameHeight = $option['cutImg']['h'];
			
			if( $autoAlign == true ) {

				$distX = ( $frameWidth - $newThumbWidht ) / 2;
				$distY = ( $frameHeight - $newThumbHeight ) / 2;
			}
		}
		
		
		
	
		$img = imagecreatetruecolor( $frameWidth, $frameHeight );

		imagealphablending( $img, true );
		$transparent = imagecolorallocatealpha( $img, 0, 0, 0, 127 );
			
		imagefill( $img, 0, 0, $transparent );
		
		imagecopyresampled( $img, $thumb, $distX, $distY, 0, 0, $newThumbWidht, $newThumbHeight, imagesx( $thumb ), 
		imagesy( $thumb ) );
		
		imagealphablending( $img, false );

		imagesavealpha( $img, true );
		
		
		
		$color = imagecolorallocate( $img, 0, 0, 0 );

		$x1 = -1;
		$y1 = -1;
		
		$x2 = imagesx( $img );
		$y2 = imagesy( $img );

		for( $th = 0; $th < $option['border']; ++$th ) {
			$x1 += 1;
			$y1 += 1;
			
			$x2 -= 1;
			$y2 -= 1;
			
			imagerectangle( $img, $x1, $y1, $x2, $y2, $color );		
		}
		
		
		
		
		if( !empty( $option['showExample'] ) ) {
			
			header( 'Content-type: image/png' );
		
			imagepng( $img );
		}
		
		if( !empty( $option['saveFile'] ) ) {
			
			$ex = explode( '.', $option['saveFile'] );
			
			$extension = strtolower( $ex[count($ex)-1] );

			if( $extension == 'jpg' )
				imagejpeg( $img, $option['saveFile'] );
			elseif( $extension == 'gif' )
				imagegif( $img, $option['saveFile'] );
			elseif( $extension == 'png' )
				imagepng( $img, $option['saveFile'] );
		}
			
		imagedestroy( $img );	
	}

	//
	//
	public function output( $image_type = IMAGETYPE_JPEG ) {
		
		if( $image_type == IMAGETYPE_JPEG )
			imagejpeg($this->image);
		elseif( $image_type == IMAGETYPE_GIF )
			imagegif($this->image);
		elseif( $image_type == IMAGETYPE_PNG )
			imagepng( $this->image );
			
		imagedestroy($this->image );
	}

	
	
	//
	//
	public function load( $filename ) {
		
		$this->image_info = getimagesize( $filename );

		list( $this->image_width, $this->image_height, $this->image_type ) = $this->image_info;
		
		if( $this->image_type == IMAGETYPE_JPEG )
			$this->image = imagecreatefromjpeg( $filename );

		elseif( $this->image_type == IMAGETYPE_GIF )
			$this->image = imagecreatefromgif( $filename );

		elseif( $this->image_type == IMAGETYPE_PNG )
			$this->image = imagecreatefrompng( $filename );
	}
	
	
	//$startPoint = array( $x1, $y1 );
	function imagelinethick( $image, $startPoint, $endPoint, $color, $thick = 1 ) {
		
		//$endPoint = array( $x2, $y2 );
		
		
		if ( $thick == 1 ) {
			return imageline( $image, $startPoint[0], $startPoint[1], $endPoint[0], $endPoint[1], $color );
		}
		
		$t = $thick / 2 - 0.5;
		if ($startPoint[0] == $endPoint[0] || $startPoint[1] == $endPoint[1]) {
			return imagefilledrectangle( $image, round( min( $startPoint[0], $endPoint[0] ) - $t), round(min($startPoint[1], $endPoint[1] ) - $t ), round( max( $startPoint[0], $endPoint[0] ) + $t ), round( max( $startPoint[1], $endPoint[1] ) + $t ), $color);
		}
		$k = ( $endPoint[1] - $startPoint[1] ) / ( $endPoint[0] - $startPoint[0] ); //y = kx + q
		
		$a = $t / sqrt( 1 + pow( $k, 2 ) );
		
		$points = array(
			round( $startPoint[0] - ( 1 + $k ) * $a ), round( $startPoint[1] + ( 1 - $k ) * $a ),
			round( $startPoint[0] - ( 1 - $k ) * $a ), round( $startPoint[1] - ( 1 + $k ) * $a ),
			round( $endPoint[0] + ( 1 + $k )* $a ), round( $endPoint[1] - ( 1 - $k ) * $a ),
			round( $endPoint[0] + ( 1 - $k ) * $a ), round( $endPoint[1] + ( 1 + $k )* $a ),
		);
		imagefilledpolygon($image, $points, 4, $color);
		return imagepolygon($image, $points, 4, $color);
	}


	//
	//
	public function crop_img( $img_temp = NULL, $img_name = 'test' ) {

		if( !empty( $img_temp ) )
			$this->load( $img_temp );

		$ratio = $_REQUEST['wind_size'] / $this->image_width;

		$exif = exif_read_data( $img_temp );

		$new_width = $this->image_width * $ratio;

		$new_height = $this->image_height * $ratio;

		if( $this->image_width <= $this->image_height )
			$this->resizeToWidth( $new_width, $new_height );

		if( $this->image_height <= $this->image_width )
			$this->resizeToHeight( $new_height, $new_width );

		$canvas = imagecreatetruecolor( $_REQUEST['w'], $_REQUEST['h'] );

		imagecopy( $canvas, $this->image, 0, 0, $_REQUEST['x'], $_REQUEST['y'], $_REQUEST['w'], $_REQUEST['h'] );

		$this->image = $canvas;


		//$this->rotate( $degree = 90 );
		if ( isset( $exif['Orientation'] ) ) {

			switch( $exif['Orientation'] ) {

				case 3: // 180 rotate left
				$this->rotate( $degree = 180 );
				//$image->imagerotate($upload_path . $newfilename, 180, -1);
				break;


				case 6: // 90 rotate right
				$this->rotate( $degree = -90 );
				//$image->imagerotate($upload_path . $newfilename, -90, -1);
				break;

				case 8:    // 90 rotate left
				$this->rotate( $degree = 90 );
				//$image->imagerotate($upload_path . $newfilename, 90, -1);
				break;
			}
		}

		$this->save( $img_name );
	}

	public function sendFile() {

	}

	//
	//
	public function cut_img( $img_temp = NULL, $img_name = 'test', $new_width = 170, $new_height = 115, $cut = true, $center_cut = true, $left = 0, $top = 0, $resize = false ) {

		if( !empty( $img_temp ) )
			$this->load( $img_temp );

		//$exif = exif_read_data( $img_temp );

		if( $resize ) {
			if( $this->image_width <= $this->image_height )
				$this->resizeToWidth( $new_width, $new_height );

			if( $this->image_height <= $this->image_width )
				$this->resizeToHeight( $new_height, $new_width );
		}

		$canvas = imagecreatetruecolor( $new_width, $new_height );

		//
		//cut_point
		if( $center_cut ) {
			$left = ( $this->image_width - $new_width ) / 2;
			$top = ( $this->image_height - $new_height ) / 2;
		}

		if( $cut ) {
			imagecopy( $canvas, $this->image, 0, 0, $left, $top, $this->image_width, $this->image_height );
			$this->image = $canvas;
		}

		//$this->rotate( $degree = 90 );
		if ( isset( $exif['Orientation'] ) ) {

			switch( $exif['Orientation'] ) {

				case 3: // 180 rotate left
				$this->rotate( $degree = 180 );
				//$image->imagerotate($upload_path . $newfilename, 180, -1);
				break;

				case 6: // 90 rotate right
				$this->rotate( $degree = -90 );
				//$image->imagerotate($upload_path . $newfilename, -90, -1);
				break;

				case 8:    // 90 rotate left
				$this->rotate( $degree = 90 );
				//$image->imagerotate($upload_path . $newfilename, 90, -1);
				break;
			}
		}


		$this->save( $img_name );
	}



	//
	//
	public function getImageInfo() {
		$data = exif_read_data('C:\DSC_0017.jpg');
		arr($data);
	}

	//
	//
	public function rotate( $degree = 0 ) {
		$this->image = imagerotate($this->image, $degree, 0);
	}

	//
	//
	public function save( $filename, $image_type = IMAGETYPE_JPEG, $compression = 75, $permissions = null )
	{

		if( $image_type == IMAGETYPE_JPEG )
			imagejpeg( $this->image, $filename, $compression );

		elseif( $image_type == IMAGETYPE_GIF )
			imagegif($this->image,$filename);

		elseif( $image_type == IMAGETYPE_PNG )
			imagepng( $this->image, $filename );

		if( $permissions != null)
			chmod($filename,$permissions);
	}


	//
	//
	public function getWidth()
	{
		return imagesx( $this->image );
	}

	//
	//
	public function getHeight()
	{
		return imagesy( $this->image );
	}

	//
	//
	public function resizeToHeight( $height, $min_w = false )
	{
		$ratio = $height / $this->getHeight();
		
		$width = ceil( $this->getWidth() * $ratio );
		
		if( $min_w && $width < $min_w )
			return $this->resizeToWidth( $min_w );
		
		$this->resize( $width, $height );
	}

	//
	//
	public function resizeToWidth( $width, $min_h = false )
	{
		$ratio = $width / $this->getWidth();
		
		$height = $this->getheight() * $ratio;
		
		if( $min_h && $height < $min_h )
			return $this->resizeToHeight( $min_h );
		
		$this->resize( $width, $height );
	}

	//
	//
	public function scale( $scale )
	{
		$width = $this->getWidth() * $scale / 100;
		$height = $this->getheight() * $scale / 100;
		$this->resize( $width, $height );
	}

	//
	//
	public function resize($width,$height)
	{
		$this->image_width = $width;
		
		$this->image_height = $height;
		
		$new_image = imagecreatetruecolor( $width, $height );
		
		imagecopyresampled( $new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight() );
		
		$this->image = $new_image;
		//var_dump($new_image);
	}

	public function uploadOther( $tmpName, $targetPath, $file_name, $fileName )
	{
		if( $this->findexts( $file_name ) ) {
			$file = $this->findexts($file_name); //File extension
			$targetPath = $targetPath .'/' . $fileName.'.'.$file;
			move_uploaded_file($tmpName, $targetPath);
		}
	}

	public function findexts($filename)
	{
		$filename = strtolower($filename) ;
		$ext = substr(strrchr($filename,'.'),1);
		$accepts = array('doc','docx','txt');
		if(!in_array($ext, $accepts))
			return false;
		return $ext;
	}

}

