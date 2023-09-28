<?php
interface OSIComponent {
    /**
     * Set the content of the component.
     * @param string $content The content to set
     */
    public function setContent($content);

    /**
     * Get the HTML ID of the containing element.
     * @return string The ID for the container of the Component
     */
    public function getHTMLID();

    /**
     * Push the HTML representation of the component to the DOM.
     * @param bool $return Return the HTML representation instead of echo-ing it. 
     */
    public function represent($return = false);

}