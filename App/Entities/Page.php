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
        return Comment::getAll(["page_id" => $this->id]);
    }

    /**
     * @return Page|bool
     */
    public function getParent()
    {
        return Page::get($this->parent_page_id);
    }

    /**
     * @return Page[]|bool
     */
    public function getChildren()
    {
        return Page::getAll(["parent_page_id" => $this->parent_page_id]);
    }


    public function delete()
    {
        $children = $this->getChildren();
        foreach ($children as $child) {
            $child->update(["parent_page_id" => null]);
        }

        return parent::delete();
    }

}
