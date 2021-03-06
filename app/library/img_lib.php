                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         <?php
/**
 *
 */
class Img_lib
{

  private $image;
  /**
	 * Original image width
	 *
	 * @var int
	 */
  private $original_w;

  /**
	 * Original image height
	 *
	 * @var int
	 */
  private $original_h;

  /**
	 * Image format
	 *
	 * @var string
	 */
  private $extension;


  private $newImage;


  /**
   * New image height
   *
   * @var int
   */
  private $newHeight;


  /**
   * New image width
   *
   * @var int
   */
  private $newWidth;

  private $pos_x;
  private $pos_y;

  private $source_w;
  private $source_h;

  protected $dest_x = 0;
  protected $dest_y = 0;


  /**
	 * Class constructor requires to send through the image filename
	 *
	 * @param string $fileName - Filename  of the image you want to resize
	 */

  function __construct($fileName)
  {
    if (file_exists($fileName))
    {
      self::setImage($fileName);
    }
    else
    {
			throw new Exception('Image ' . $fileName . ' can not be found, try another image.');
		}
  }


  /**
	 * Set the image variable by using image create
	 *
	 * @param string $filename - The image filename
	 */

   public function setImage($fileName)
   {
     $file=getImagesize($fileName);
     $this->extension =$file['mime'];


     switch ($this->extension)
     {
       case 'image/jpg':
       case 'image/jpeg':
         $this->image=imagecreatefromjpeg($fileName);
       break;

       case 'image/png':
         $this->image=imagecreatefrompng($fileName);
       break;

       case 'image/gif':
         $this->image=imagecreatefromgif($fileName);
       break;

       default:
         throw new Exception("File is not an image, please use another file type.", 1);
       break;
     }

   }


  /**
   * get current image width
   *
   * @return integer
   */

  public function getWidth()
  {
     return  $this->original_w  = imagesx($this->image);
  }


  /**
   * get current image height
   *
   * @return integer
   */
  public function getHeight()
  {
    return  $this->original_h = imagesy($this->image);
  }

  /**
   * Calculate the current aspect ratio
   *
   * @return float
   */
  public function getRatio()
  {
      return $this->width / $this->height;
  }

  /**
	 * Resize the image to these set dimensions
	 *
	 * @param  int $width        	- Max width of the image
	 * @param  int $height       	- Max height of the image
	 * @param  string $resizeOption - Scale option for the image
	 *
	 * @return bool
	 */


  public function resize($width,$height,$resizeOption="default")
  {
    switch(strtolower($resizeOption))
  {
    case 'exact':
      $this->newWidth = $width;
      $this->newHeight = $height;
    break;

    case 'maxwidth':
      $this->newWidth  = $width;
      $this->newHeight = $this->resizeHeightByWidth($width);
    break;

    case 'maxheight':
      $this->newWidth  = $this->resizeWidthByHeight($height);
      $this->newHeight = $height;
    break;

    default:
      if($this->getWidth() > $width || $this->getHeight() > $height)
      {
        if ( $this->getWidth() > $this->getHeight() )
        {
             $this->newWidth  = $width;
             $this->newHeight = $this->resizeHeightByWidth($width);
        }
        else if( $this->getWidth() < $this->getHeight())
        {
          $this->newHeight = $height;
          $this->newWidth  = $this->resizeWidthByHeight($height);
        }
        else
        {
          $this->newWidth = $width;
          $this->newHeight = $height;
        }
      }
     else
      {
          $this->newWidth = $width;
          $this->newHeight = $height;
      }
    break;
    }
    // $this->newImage = imagecreatetruecolor($this->newWidth, $this->newHeight);
    //  imagecopyresampled(
    //    $this->newImage,
    //    $this->image, 0, 0, 0, 0,
    //    $this->newWidth,
    //    $this->newHeight,
    //    $this->source_w,
    //    $this->source_h
    //  );
        $this->source_x = 0;
        $this->source_y = 0;
        $this->dest_w = $width;
        $this->dest_h = $height;
        $this->source_w = $this->getWidth();
        $this->source_h = $this->getHeight();
        return $this;

  }


  /**
	 * Get the resized height from the width keeping the aspect ratio
	 *
	 * @param  int $width - Max image width
	 *
	 * @return Height keeping aspect ratio
	 */

   private function resizeHeightByWidth(int $width)
   {
  		return floor(($this->getWidth() / $this->getHeight()) * $width);
   }



   /**
	 * Get the resized width from the height keeping the aspect ratio
	 *
	 * @param  int $height - Max image height
	 *
	 * @return Width keeping aspect ratio
	 */

	private function resizeWidthByHeight(int $height)
	{
		return floor(($this->getWidth / $this->getHeight) * $height);
	}


  /**
	 * Save the image as the image type the original image was
	 *
	 * @param  String[type] $savePath     - The path to store the new image
	 * @param  string $imageQuality 	  - The qulaity level of image to create
	 *
	 * @return Saves the image
	 */



   public function save($savePath, $imageQuality = "100", $permissions = null)
   {

       $dest_image = imagecreatetruecolor($this->newWidth, $this->newHeight);

       // imageinterlace($this->newImage, $this->interlace);
       imagecopyresampled(
           $dest_image,
           $this->image,
           $this->dest_x,
           $this->dest_y,
           $this->pos_x,
           $this->pos_y,
           $this->newWidth,
           $this->newHeight,
           $this->source_w,
           $this->source_h
       );
       switch ($this->extension) {
         case 'image/jpg':
         case 'image/jpeg':
           // Check PHP supports this file type
             if (imagetypes() & IMG_JPG)
             {
                 imagejpeg($dest_image, $savePath, $imageQuality);
             }
         break;

         case 'image/gif':
           // Check PHP supports this file type
             if (imagetypes() & IMG_GIF)
             {
                 imagegif($dest_image, $savePath);
             }
         break;

         case 'image/png':
             $invertScaleQuality = 9 - round(($imageQuality/100) * 9);

             // Check PHP supports this file type
             if (imagetypes() & IMG_PNG)
             {
                 imagepng($dest_image, $savePath, $invertScaleQuality);
             }
         break;
       }
      //  if ($permissions) {
      //      chmod($filename, $permissions);
      //  }

       imagedestroy($dest_image);
   }


  /**
	 * Image Crop
	 *
	 * This is a wrapper function that chooses the proper
	 * cropping function based on the protocol specified
   *
	 * @param  int $width        	-  width of the image
 	 * @param  int $height       	-  height of the image
   * @param  int $pos_x         - coordinate of the cropped region's top left corner
   * @param  int $pos_y         - coordinate of the cropped region's top left corner
	 * @return	bool
	 */
   public function freecrop($width, $height, $pos_x = false, $pos_y = false)
   {
       $this->pos_x = $pos_x;
       $this->pos_y = $pos_y;
       if($width > $this->getWidth() - $pos_x){
         $this->source_w = $this->getWidth() - $pos_x;
       } else {
         $this->source_w = $width;
       }
       if($height > $this->getHeight() - $pos_y){
         $this->source_h = $this->getHeight() - $pos_y;
       } else {
         $this->source_h = $height;
       }
       $this->newWidth = $width;
       $this->newHeight = $height;
       return $this;
   }


  public function rotate()
  {
    # code...
  }

  public function watermark()
  {
    # code...
  }
}
  ?>
