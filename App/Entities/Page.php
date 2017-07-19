<?php

namespace App\Entities;

class Page extends BasePage
{
    public $parent_page_id;

    /**
     * @return Comment[]|bool
     */
    public function getComments()
    {
        if (is_int($this->id)) {
            return Comment::getAll(["page_id" => $this->id]);
        }
        return [];
    }

    /**
     * @return Page|false
     */
    public function getParent()
    {
        if (is_int($this->parent_page_id)) {
            return Page::get($this->parent_page_id);
        }
        return false;
    }

    /**
     * @return Page[]|bool
     */
    public function getChildren()
    {
        if (is_int($this->id)) {
            return Page::getAll(["parent_page_id" => $this->id]);
        }
        return [];
    }

    public function update($data)
    {
        if (isset($data["parent_page_id"]) && $data["parent_page_id"] <= 0) {
            $data["parent_page_id"] = null;
        }
        return parent::update($data);
    }

    public function delete()
    {
        $children = $this->getChildren();
        foreach ($children as $child) {
            $child->update(["parent_page_id" => null]);
        }

        return parent::delete();
    }

    public function getLink($routeName = "page")
    {
        return parent::getLink($routeName);
    }
}
