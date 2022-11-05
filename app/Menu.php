<?php

namespace App;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';
    protected $fillable = ['name','slug'];
    //protected $hidden = ['_token'];


    public function pages()
    {
        return $this->hasOne('App\Pages');
    }
    

    // Menu builder function, parentId 0 is the root
    public function build_menu($parent, $menu) {
        $html = "<ol class='dd-list'>";
        if (isset($menu['parents'][$parent])) {
            
            foreach ($menu['parents'][$parent] as $itemId) {
                if(!isset($menu['parents'][$itemId]))
                {
                    $id = $menu['items'][$itemId]['id'];
                    $html .= "<li class='dd-item dd3-item'><div class='dd-handle dd3-handle'></div><div class='dd3-content'>".$menu['items'][$itemId]['name']."<span style='float:right'><a href='javascript:void(0)' onclick='loadPage(".$id.")'>Assign Page</a> | <a href='".url("/")."/admin/menus/edit/".$id."'>Edit</a> | <a class='deletemenu' id='".$id."' href='javascript:void(0)'>Delete</a></span></div></li>";
                }
                if(isset($menu['parents'][$itemId]))
                {
                    $id = $menu['items'][$itemId]['id'];
                    $html .= "</ol>";
                    $html .= "<ol class='dd-list'>
                    <li class='dd-item dd3-item'><div class='dd-handle dd3-handle'></div><div class='dd3-content'>".$menu['items'][$itemId]['name']."<span style='float:right'><a href='javascript:void(0)' onclick='loadPage(".$id.")'>Assign Page</a> | <a href='".url("/")."/admin/menus/edit/".$id."'>Edit</a> | <a class='deletemenu' id='".$id."' href='javascript:void(0)'>Delete</a></span></div>";
                    $html .= $this->build_menu($itemId, $menu);
                    $html .= "</li></ol>";
                }
            }
            //$html .= "</ol>";
        }
        return $html;
    }

    public function build_menu1($parent, $menu) {
        $html = "";
        if (isset($menu['parents'][$parent])) {
            //$html .= "<ul>";
            foreach ($menu['parents'][$parent] as $itemId) {


                if(!isset($menu['parents'][$itemId]))
                {
                    $id = $menu['items'][$itemId]['slug'];
                    
                    $html .= "<li class='nav-item'><a class='nav-link' href='".url("/pages/$id")."'>".$menu['items'][$itemId]['name']."</a></li>";
                }
                if(isset($menu['parents'][$itemId]))
                {   
                    if($menu['items'][$itemId]['parent_id']==0){
                        $id = $menu['items'][$itemId]['slug'];
                        $html .= "<li class='nav-item dropdown'><a class='nav-link dropdown-toggle' href='".url("/pages/$id")."'>".$menu['items'][$itemId]['name']."</a>";
                    }
                    if($parent==0){
                        $html .= "<ul class='dropdown-menu'>";
                    }
                    //$html .= "<li><a href='".$menu['items'][$itemId]['link']."'>".$menu['items'][$itemId]['name']."</a>";
                    $html .= $this->build_menu1($itemId, $menu);
                    $html .= "</li>";
                    if($parent==0){
                        $html .= "</ul>";
                    }

                    if($menu['items'][$itemId]['parent_id']==0){
                        $html .= "</li>";
                    }
                }
            }
            //$html .= "</ul>";
        }
        return $html;
    }
    
    // public function pages()
    // {
    //     return $this->belongsToMany('App\Pages','menu_page');
    // }

    // public function getSelect($is_select=false){
    //     $select = Role::orderBy('created_at','DESC');
    //     if($is_select){
    //         return $select;
    //     }
    //     return $select->get();
    // }
}
