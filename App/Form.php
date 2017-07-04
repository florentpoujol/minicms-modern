<?php

namespace App;

class Form
{
    /**
     * @var string The name of the form, mostly used as prefix for CSRF protection
     */
    private $name;

    /**
     * @var array Assoc array used to prefill the inputs default values
     */
    private $data;

    public function __construct($name, $data = [])
    {
        $this->name = $name;
        $this->data = $data;
    }

    public function open($target, $method = "POST")
    {
        echo '<form action="'.$target.'" method="'.$method.'">
        ';
    }

    public function close($addCSRFProtection = true)
    {
        if ($addCSRFProtection) {
            $this->hidden($this->name."_csrf_token", Security::createCSRFTokens($this->name));
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
     */
    public function input($type, $name, $attributes = [])
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
            $tmpLabel = Lang::get("formlabel.$label");
            if ($tmpLabel !== "formlabel.$label") {
                $label = $tmpLabel;
            } else {
                $tmpLabel = Lang::get($label);
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

    public function text($name, $attributes = null)
    {
        $this->input("text", $name, $attributes);
    }

    public function number($name, $attributes = null)
    {
        $this->input("number", $name, $attributes);
    }

    public function email($name, $attributes = null)
    {
        $this->input("email", $name, $attributes);
    }

    public function password($name, $attributes = null)
    {
        $this->input("password", $name, $attributes);
    }

    public function hidden($name, $value)
    {
        $this->input("hidden", $name, ["value" => $value]);
    }

    public function submit($name = "", $value = "")
    {
        $this->input("submit", $name, ["value" => $value]);
    }

    /**
     * @param string $name
     * @param bool $isChecked
     * @param array|string $attributes
     */
    public function checkbox($name, $isChecked = false, $attributes = [])
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

    public function select($name, $options, $attributes = [])
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
            $tmpLabel = Lang::get("formlabel.$label");
            if ($tmpLabel !== "formlabel.$label") {
                $label = $tmpLabel;
            } else {
                $tmpLabel = Lang::get($label);
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
     * @param string $name
     * @param array $attributes
     */
    public function textarea($name, $attributes = [])
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
            $tmpLabel = Lang::get("formlabel.$label");
            if ($tmpLabel !== "formlabel.$label") {
                $label = $tmpLabel;
            } else {
                $tmpLabel = Lang::get($label);
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

    public function br($count = 1)
    {
        while ($count--) {
            echo "<br>
            ";
        }
    }
}
