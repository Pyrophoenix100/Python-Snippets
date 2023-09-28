<?php
include_once ($_SERVER['DOCUMENT_ROOT'] . "/shared/components/Component.php");
class OSIModal implements OSIComponent {
    //Unique-ify-ing the class, so that I can refer to them individually by id. Instances start at 1000 for consistency with the CRM, but this can absolutely be modified
    static private $instances = 1000;
    public $id;

    public $content, $title = "";

    // Not sure if I'm actually gonna implement this, could be useful, but might be too niche
    private $width, $height;


    /**
     * Create a new OSIModal object. Optional arguments to set title and content on the fly.
     * @param string $content Sets the content of the Modal. This is the HTML that is inside the box.
     * @param string $title Sets the title of the Modal. This is the text that appears at the top of the box. HTML compat. is not tested, use at your own risk. 
     */
    public function __construct(string $title = null, string $content = null) {
        $this->id = self::$instances;
        self::$instances++;
        if ($content != null) {
            $this->content = $content;
        }
        if ($title != null) {
            $this->title = $title;
        }
    }

    /**
     * Adds HTML to the content of the Modal. Functionally identical to $this->content = $content;
     * @param string $content HTML representation of the content to display. Is not sanitized.
     */
    public function setContent ($content) {
        $this->content = str_replace("'", '\'', $content);
    }

    /**
     * Sets the title of the Modal.
     * @param string $title The title to set for the modal. Is not sanitized. 
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * Returns the HTML id for the modal object. Equivalent to ("modal_" . $id).
     * @return string The HTML id.
     */
    public function getHTMLID() {
        return "modal_" . $this->id;
    }

    /**
     * Echoes the JS function used to open the modal. Will echo something like document.getElementById('modal_1000').showModal();.
     * @param bool $return Return the HTML representation instead.
     * @return string|null
     */
    public function openFunction($return = false) {
        $string = "document.getElementById('{$this->getHTMLID()}').showModal();";
        if ($return) {
            return $string;
        } else {
            echo $string;
        }
    }

    /**
     * Echoes a simple HTML button. You can provide a arguement to set the text of the button, otherwise it will be "Open Modal".
     * @param string $text Sets the text of the button.
     */
    public function openButton($text = "Open Modal", $return = false) {
        $string = <<<MODALBUTTON
        <button onclick="document.getElementById('{$this->getHTMLID()}').showModal();">$text</button>
MODALBUTTON;
        if ($return) {
            return $string;
        } else {
            echo $string;
        }
    }

    /**
     * Echoes an `a` element with a onclick to open the modal
     */
    public function openText($text = "Open Modal", $return = false) {
        $string = <<<MODALTEXT
        <a onclick="document.getElementById('{$this->getHTMLID()}').showModal();">$text</a>
MODALTEXT;
        if ($return) {
            return $string;
        } else {
            echo $string;
        }
    }


    /**
     * Echoes an image to the DOM that will open the modal when clicked.
     * @param string $src The URL of the image to use.
     * @param string $width The HTML width of the image. Units are optional, otherwise px is assumed.
     * @param string $height The HTML height of the image. Units are optional, otherwise px is assumed.
     */
    public function openImage($src, $width = "32px", $height = "32px", $return = false) {
        $string =<<<MODALIMAGE
        <img src='$src' width='$width' height='$height' onclick="document.getElementById('modal_{$this->id}').showModal();">
MODALIMAGE;
        if ($return) {
            return $string;
        } else {
            echo $string;
        }
    }

    public function represent($return = false) {
        $output = <<<MODAL_HEAD
        <dialog class='modal' id='modal_{$this->id}'>
        <div class='modal-header'>
            <img src='https://www.1si.biz/admin/images/minilogo.gif'>
            <b style='margin: 0px 1eem 0px 1em; color: white;'> {$this->title} </b>
            <img style="float:right;" height='24px' width='24px' src="https://www.1si.biz/images/classy/24x24/delete.png" onClick='document.getElementById("{$this->getHTMLID()}").close();'></img>
        </div>
        <div class='modal-content'>
MODAL_HEAD;

        $output .= $this->content;
        $output .= <<<MODAL_FOOT
        </div>
        </dialog>
MODAL_FOOT;
        if ($return) {
            return $output;
        } else {
            echo $output;
        }
    }
}
?>