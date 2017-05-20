<?php

namespace App;

class Form
{
    private $data;

    public function __construct($data = [])
    {
        $this->data = $data;
    }


    public function open($target, $method = "POST")
    {
        echo '<form action="'.$target.'" method="'.$method.'">';
    }

    public function close()
    {
        echo '</form>';
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
            $content .= "<label> $label";
        }

        $content .= '<input type="'.$type.'" name="'.$name.'"';

        if (isset($this->data[$name])) {
            $value = htmlspecialchars($this->data[$name]);
            $content .= ' value="'.$value.'">';
        }

        foreach ($attributes as $attr => $value) {
            if ($attr === "checked") {
                $content .= ' '.$attr;
            }
            else {
                $content .= ' '.$attr.'="'.$value.'"';
            }
        }

        $content .= ">";

        if ($label !== "") {
            $content .="</label>";
        }
    }

    public function text($name, $attributes = null)
    {
        $this->input("text", $name, $attributes = null);
    }

    public function number($name, $attributes = null)
    {
        $this->input("number", $name, $attributes = null);
    }

    public function email($name, $attributes = null)
    {
        $this->input("email", $name, $attributes = null);
    }

    public function password($name, $attributes = null)
    {
        $this->input("password", $name, $attributes = null);
    }


    public function hidden($name, $value)
    {
        $this->input("hidden", $name, ["value" => $value]);
    }


    public function submit($name = "", $value = "")
    {
        $this->input("submit", $name, ["value" => $value]);
    }


    public function checkbox($name, $isChecked = false, $attributes = [])
    {
        if ($isChecked) {
            $attributes["checked"] = "";
        }
        else {
            unset($attributes["checked"]);
        }

        $this->input("checkbox", $name, $attributes);
    }
}
