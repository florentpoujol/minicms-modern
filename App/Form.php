<?php

namespace App;

class Form
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Lang
     */
    protected $lang;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var string The name of the form, mostly used as prefix for CSRF protection
     */
    private $name = "";

    /**
     * @var array Assoc array used to prefill the inputs default values
     */
    private $data = [];

    public function __construct(Session $session, Lang $lang, Config $config)
    {
        $this->session = $session;
        $this->lang = $lang;
        $this->config = $config;
    }

    /**
     * Set the form name and data from which it will be populated.
     * The name is the base of the generated CSRF token.
     */
    public function setup(string $name, array $data = [])
    {
        $this->name = $name;
        $this->data = $data;
    }

    public function open(string $target, string $method = "POST", bool $forFileUpload = false)
    {
        if ($forFileUpload) {
            $forFileUpload = ' enctype="multipart/form-data"';
        } else {
            $forFileUpload = "";
        }
        echo '<form action="' . $target . '" method="' . $method . '"' . $forFileUpload . '>        
        ';
    }

    public function close(bool $addCSRFProtection = true)
    {
        if ($addCSRFProtection) {
            $this->hidden($this->name."_csrf_token", $this->session->createCSRFToken($this->name));
        }

        echo '</form>
        ';
    }

    /**
     * @param string $type The input type (text, number, ...)
     * @param string $name The value for the name attribute
     * @param array $attributes Assoc array containing additional attributes.
     * May contains a "value" key, which override the value that may be found in the form's data.
     * May contains a "label" key, which may be a language keys (automatically prefixed with "formlabel").
     * @param string|array $attributes
     */
    public function input(string $type, string $name, $attributes = [])
    {
        $label = "";
        if (is_string($attributes)) {
            $label = $attributes;
            $attributes = [];
        }

        if (isset($attributes["label"])) {
            $label = $attributes["label"];
            unset($attributes["label"]);
        }

        $content = "";

        if ($label !== "") {
            $tmpLabel = $this->lang->get("formlabel.$label");
            if ($tmpLabel !== "formlabel.$label") {
                $label = $tmpLabel;
            } else {
                $tmpLabel = $this->lang->get($label);
                if ($tmpLabel !== $label) {
                    $label = $tmpLabel;
                }
            }

            $content .= "<label> $label
            ";
        }

        $content .= '<input type="'.$type.'" name="'.$name.'"';

        // value
        $value = null;
        if ($type !== "password" && isset($this->data[$name])) {
            $value = $this->data[$name];
        } elseif (isset($attributes["value"])) {
            $value = $attributes["value"];
            unset($attributes["value"]);
        }

        $noValueTypes = ["checkbox", "radio"];
        if (! in_array($type, $noValueTypes) && $value !== null) {
            $content .= ' value="'.htmlentities($value).'"';
        } elseif ($type === "checkbox") {
            if (is_bool($value) || is_int($value)) {
                if ($value) {
                    $attributes["checked"] = "";
                } else {
                    unset($attributes["checked"]);
                }
            }
        }

        // other attributes
        $noValueAttrs = ["checked", "selected", "required"];
        foreach ($attributes as $attr => $value) {
            $content .= ' '.$attr;

            if (! in_array($attr, $noValueAttrs)) {
                $content .= '="'.$value.'"';
            }
        }

        $content .= "> <br>
        ";

        if ($label !== "") {
            $content .="</label>
            ";
        }

        echo $content;
    }

    /**
     * @param string|array|null $attributes
     */
    public function text(string $name, $attributes = null)
    {
        $this->input("text", $name, $attributes);
    }

    /**
     * @param string|array|null $attributes
     */
    public function number(string $name, $attributes = null)
    {
        $this->input("number", $name, $attributes);
    }

    /**
     * @param string|array|null $attributes
     */
    public function email(string $name, $attributes = null)
    {
        $this->input("email", $name, $attributes);
    }

    /**
     * @param string|array|null $attributes
     */
    public function password(string $name, $attributes = null)
    {
        $this->input("password", $name, $attributes);
    }

    public function file(string $name, array $attributes = [])
    {
        if (! isset($attributes["accept"])) {
            $attributes["accept"] = ".jpeg, .jpg, image/jpeg, .png, image/png, .pdf, application/pdf, .zip, application/zip";
        }
        $this->input("file", $name, $attributes);
    }

    public function hidden(string $name, string $value)
    {
        $this->input("hidden", $name, ["value" => $value]);
    }

    public function submit(string $name = "", string $value = "", array $attributes = [])
    {
        $attributes["value"] = $value;
        $this->input("submit", $name, $attributes);
    }

    /**
     * @param string|array $attributes
     */
    public function checkbox(string $name, bool $isChecked = false, $attributes = [])
    {
        if (is_string($attributes)) {
            $attributes = ["label" => $attributes];
        }

        if ($isChecked) {
            $attributes["checked"] = "";
        }
        else {
            unset($attributes["checked"]);
            // passing false to the method cannot force the field
            // to be unchecked when the data passed to the form says it is checked
        }

        $this->input("checkbox", $name, $attributes);
    }

    /**
     * @param string|array $attributes
     */
    public function select(string $name, array $options, $attributes = [])
    {
        $label = "";
        if (is_string($attributes)) {
            $label = $attributes;
            $attributes = [];
        }

        if (isset($attributes["label"])) {
            $label = $attributes["label"];
            unset($attributes["label"]);
        }

        $content = "";

        if ($label !== "") {
            $tmpLabel = $this->lang->get("formlabel.$label");
            if ($tmpLabel !== "formlabel.$label") {
                $label = $tmpLabel;
            } else {
                $tmpLabel = $this->lang->get($label);
                if ($tmpLabel !== $label) {
                    $label = $tmpLabel;
                }
            }

            $content .= "<label> $label
            ";
        }

        $defaultValue = null;
        if (isset($attributes["value"])) {
            $defaultValue = $attributes["value"];
            unset($attributes["value"]);
        }

        $content .= '<select name="'.$name.'"';

        // other attributes
        foreach ($attributes as $attr => $value) {
            $content .= " $attr='$value'";
        }

        $content .= ">
        ";

        // options
        $valueFromData = null;
        if (isset($this->data[$name])) {
            $valueFromData = $this->data[$name];
        }

        foreach ($options as $_value => $text) {
            $selected = "";
            if (
                ($valueFromData !== null && $valueFromData === $_value) ||
                ($valueFromData === null && $defaultValue === $_value)
            ) {
                $selected = "selected";
            }
            $content .= '<option value="'.$_value.'" '.$selected.'>'.$text.'</option>
            ';
        }

        $content .= "</select> <br>
        ";

        if ($label !== "") {
            $content .="</label>
            ";
        }

        echo $content;
    }

    /**
     * @param string|array $attributes
     */
    public function textarea(string $name, $attributes = [])
    {
        $label = "";
        if (is_string($attributes)) {
            $label = $attributes;
            $attributes = [];
        }

        if (isset($attributes["label"])) {
            $label = $attributes["label"] . "<br>";
            unset($attributes["label"]);
        }

        $content = "";

        if ($label !== "") {
            $tmpLabel = $this->lang->get("formlabel.$label");
            if ($tmpLabel !== "formlabel.$label") {
                $label = $tmpLabel;
            } else {
                $tmpLabel = $this->lang->get($label);
                if ($tmpLabel !== $label) {
                    $label = $tmpLabel;
                }
            }

            $content .= "<label> $label
            ";
        }

        $value = "";
        if (isset($attributes["value"])) {
            $value = $attributes["value"];
            unset($attributes["value"]);
        }
        if (isset($this->data[$name])) {
            $value = $this->data[$name];
        }

        $content .= '<textarea name="'.$name.'"';
        foreach ($attributes as $attr => $attrValue) {
            $content .= " $attr='$attrValue'";
        }

        $content .= ">$value</textarea> <br>
        ";

        if ($label !== "") {
            $content .="</label>
            ";
        }

        echo $content;
    }

    public function recaptcha()
    {
        $siteKey = $this->config->get("recaptcha_site_key", "");
        if ($siteKey !== "") {
            echo '<div class="g-recaptcha" data-sitekey="' . $siteKey . '"></div>';
            echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
        }
    }

    public function br(int $count = 1)
    {
        while ($count--) {
            echo "<br>
            ";
        }
    }
}
