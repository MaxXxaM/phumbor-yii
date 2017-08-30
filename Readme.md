# Phumbor for Yii 2

This extension allows you to implement interaction with Thumbor in Yii 2.


## Install

#### Dependency:

* [Базовое расширение Phumbor PHP](https://github.com/99designs/phumbor)


**1** Install via composer. Add the following code to the file `composer.json`.
```json
{
	"require": {
		"maxxxam/phumbor-yii": "~0.1"
	}
}
```

**2** Install via composer

```bash
$ composer install
```

or update with existing packages

```bash
$ composer update
```

**3** Add the following into config app

```
'thumbor' => [
    'class' => 'PhumborYii\Phumbor',
    'server' => 'http://127.0.0.1',
    'uploadServer' => 'http://<thumbor_host>:<thumbor_port>',
    'secret' => '<thumbor_secret>',
    'defaultFileName' => '<thumbor_default_image_name>'
],
```

## Usage

### Upload files
```
foreach ($_FILES as &$file){
    $urlUpload = Yii::$app->thumbor->upload($file);
}
```

### Get ID image
```
$idImage = Yii::$app->thumbor->getImageId($urlUpload);
```

### Remove file
```
Yii::$app->thumbor->remove($idImage);
```

### Resize image (100 x 100)
```
Yii::$app->thumbor->getUrlBuilder($urlUpload)->resize(100, 100);
```

Other available thumbor methods described [here](https://github.com/thumbor/thumbor/wiki/Usage)

# Example classes for images manipulating

```
class Image extends File implements ImageInterface
{
    public function getSrc():string
    {
        return Yii::$app->thumbor->getFullUrl($this->id_external);
    }

    public function getMeta($src = null): array
    {
        $source = $src ?: Yii::$app->thumbor->getUrlBuilder($this->getSrc(), true)->metadataOnly(true)->__toString();
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $source);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = json_decode(curl_exec($curl), true);
        curl_close($curl);
        return $result['thumbor'] ?? null;
    }

    public function getVariants(){
        return new ImageVariants($this);
    }

}
```

```
interface ImageInterface
{
    public function getSrc():string;
    public function getMeta($src = null):array;
}
```

```
abstract class AbstractImageSize extends MagicMethods implements ImageInterface
{
    private $_image;

    public function __construct(ImageInterface $image)
    {
        $this->_image = $image;
    }

    private function getUrlBuilder($source, $uploadServer = false): Builder{
        return \Yii::$app->thumbor->getUrlBuilder($source, $uploadServer);
    }

    public function getSrc():string
    {
        $builder = $this->getUrlBuilder($this->_image->getSrc());
        return $this->applyActions($builder)->__toString();
    }

    public function getMeta($src = null):array
    {
        $builder = $this->getUrlBuilder($this->_image->getSrc(), true);
        $path = $this->applyActions($builder)->metadataOnly(true)->__toString();
        return $this->_image->getMeta($path);
    }

    /**
     * Apply filters
     * @param Builder $builder
     * @return mixed
     */
    abstract protected function applyActions($builder);


}
```

```
class ImageSmall extends AbstractImageSize
{

    /**
     * @inheritdoc
     */
    protected function applyActions($builder){
        return $builder->resize(100, 100);
    }

}
```

```
class ImageVariants extends MagicMethods
{
    private $_image;

    public function __construct(ImageInterface $image)
    {
        $this->_image = $image;
    }

    public function getSmall():ImageSmall{
        return new ImageSmall($this->_image);
    }

    ...

}    
```



