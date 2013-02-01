<?php
use Phalcon\Translate\Adapter,
    Phalcon\Translate\AdapterInterface,
    Phalcon\Translate\Exception;

class Translation_GetText extends Adapter implements AdapterInterface
{

    /**
     * Translation_GetText constructor
     *
     * @throws Exception
     * @param array $options
     */
    public function __construct($options){

        if(!is_array($options)){
            throw new Exception('Invalid options');
        }

        if(!isset($options['locale'])){
            throw new Exception('Parameter "locale" is required');
        }

        if(!isset($options['file'])){
            throw new Exception('Parameter "file" is required');
        }

        if(!isset($options['directory'])){
            throw new Exception('Parameter "directory" is required');
        }

        putenv("LC_ALL=".$options['locale']);
        setlocale(LC_ALL, $options['locale']);
        bindtextdomain($options['file'], $options['directory']);
        textdomain($options['file']);
    }

    /**
     * Returns the translation string of the given key
     *
     * @param   string $translateKey
     * @param   array $placeholders
     * @return  string
     */
    public function _($translateKey, $placeholders=null){
        return $this->query($translateKey, $placeholders);
    }


    /**
     * Returns the translation related to the given key
     *
     * @param	string $index
     * @param	array $placeholders
     * @return	string
     */
    public function query($index, $placeholders=null)
    {
        if($placeholders==null){
            return gettext($index);
        }

        $translation = gettext($index);;
        if (is_array($placeholders)) {
            foreach($placeholders as $key => $value){
                $translation = str_replace('%'.$key.'%', $value, $translation);
            }
        }

        return $translation;
    }

    /**
     * Check whether is defined a translation key in the internal array
     *
     * @param 	string $index
     * @return	bool
     */
    public function exists($index)
    {
        return gettext($index) !== '';
    }

}