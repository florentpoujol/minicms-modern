<?php

namespace App;

class Form
{
    private $name;
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
            // $this->hidden($this->name."_csrf_token");
        }

        echo '</form>
        ';
    }


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
            $content .= "<label> $label
            ";
        }

        $content .= '<input type="'.$type.'" name="'.$name.'"';

        // value
        $value = null;
        if (isset($attributes["value"])) {
            $value = $attributes["value"];
            unset($attributes["value"]);
        }
        elseif (isset($this->data[$name])) {
            $value = $this->data[$name];
        }

        if (isset($value)) {
            $content .= ' value="'.htmlspecialchars($value).'"';
        }

        // other attributes
        $noValueAttrs = ["checked", "selected", "required"];

        foreach ($attributes as $attr => $value) {
            if (in_array($attr, $noValueAttrs)) {
                $content .= ' '.$attr;
            }
            else {
                $content .= ' '.$attr.'="'.$value.'"';
            }
        }

        $content .= ">
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


    public function checkbox($name, $isChecked = false, $attributes = null)
    {
        if (!isset($attributes)) {
            $attributes = [];
        }

        if ($isChecked) {
            $attributes["checked"] = "";
        }
        else {
            unset($attributes["checked"]);
        }

        $this->input("checkbox", $name, $attributes);
    }

    public function br($count = 1)
    {
        while ($count--) {
            echo "<br>
            ";
        }
    }
}
