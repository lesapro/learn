<?php

namespace Fovoso\MegaMenuExtends\Block;

/**
 * Class TopMenuExtend
 * @package Fovoso\MegaMenuExtends\Block
 */
class TopMenuExtend extends \Smartwave\Megamenu\Block\Topmenu
{
    /**
     * @return string
     */
    public function getMegaMenuMobileHtml()
    {
        $html = '';

        $categories = $this->getStoreCategories(true,false,true);

        $this->_megamenuConfig = $this->_helper->getConfig('sw_megamenu');

        $max_level = $this->_megamenuConfig['general']['max_level'];
        $html .= $this->getCustomBlockHtml('before');
        $position = 0;
        foreach($categories as $category) {
            if (!$category->getIsActive()) {
                continue;
            }

            $cat_model = $this->getCategoryModel($category->getId());

            $sw_menu_hide_item = $cat_model->getData('sw_menu_hide_item');

            if(!$sw_menu_hide_item) {
                $children = $this->getActiveChildCategories($category);
                $sw_menu_cat_label = $cat_model->getData('sw_menu_cat_label');
                $sw_menu_icon_img = $cat_model->getData('sw_menu_icon_img');
                $sw_menu_font_icon = $cat_model->getData('sw_menu_font_icon');
                $sw_menu_cat_columns = $cat_model->getData('sw_menu_cat_columns');
                $sw_menu_float_type = $cat_model->getData('sw_menu_float_type');

                if(!$sw_menu_cat_columns){
                    $sw_menu_cat_columns = 4;
                }

                $menu_type = $cat_model->getData('sw_menu_type');
                if(!$menu_type)
                    $menu_type = $this->_megamenuConfig['general']['menu_type'];

                $custom_style = '';
                if($menu_type=="staticwidth")
                    $custom_style = ' style="width: 500px;"';

                $sw_menu_static_width = $cat_model->getData('sw_menu_static_width');
                if($menu_type=="staticwidth" && $sw_menu_static_width)
                    $custom_style = ' style="width: '.$sw_menu_static_width.';"';

                $item_class = 'level0 ';
                $item_class .= $menu_type.' ';

                $menu_top_content = $cat_model->getData('sw_menu_block_top_content');
                $menu_left_content = $cat_model->getData('sw_menu_block_left_content');
                $menu_left_width = $cat_model->getData('sw_menu_block_left_width');
                if(!$menu_left_content || !$menu_left_width)
                    $menu_left_width = 0;
                $menu_right_content = $cat_model->getData('sw_menu_block_right_content');
                $menu_right_width = $cat_model->getData('sw_menu_block_right_width');
                if(!$menu_right_content || !$menu_right_width)
                    $menu_right_width = 0;
                $menu_bottom_content = $cat_model->getData('sw_menu_block_bottom_content');
                if($sw_menu_float_type)
                    $sw_menu_float_type = 'fl-'.$sw_menu_float_type.' ';
                if(count($children) > 0 || (($menu_type=="fullwidth" || $menu_type=="staticwidth") && ($menu_top_content || $menu_left_content || $menu_right_content || $menu_bottom_content)))
                    $item_class .= 'parent ';
                $activeCate = '';
                if ($position == 0) {
                    $activeCate = ' mobile-parent-active';
                }
                $html .= '<li class="ui-menu-item '.$item_class.$sw_menu_float_type. $activeCate .'" data-cateid="'.$category->getId().'">';
                $html .= '<a href="" class="level-top" title="'.$category->getName().'">';
                if ($sw_menu_icon_img && $cat_model->getLevel() != 2)
                    $html .= '<img class="menu-thumb-icon" src="' . $this->_helper->getBaseUrl().'' . $sw_menu_icon_img . '" alt="'.$category->getName().'"/>';
                elseif($sw_menu_font_icon)
                    $html .= '<em class="menu-thumb-icon '.$sw_menu_font_icon.'"></em>';
                $html .= '<span>'.$category->getName().'</span>';
                if($sw_menu_cat_label)
                    $html .= '<span class="cat-label cat-label-'.$sw_menu_cat_label.'">'.$this->_megamenuConfig['cat_labels'][$sw_menu_cat_label].'</span>';
                $html .= '</a>';
                $html .= '</li>';
            }
            $position++;
        }
        $html .= $this->getCustomBlockHtml('after');

        return $html.'<li class="ui-menu-item level0 fullwidth "><a href="/shopbrand" class="level-top" title="Brands"><span>Brands</span></a></li>';
    }

    /**
     * @return string
     */
    public function getMegaMenuMobileHtmlChild()
    {
        $html = '';

        $categories = $this->getStoreCategories(true,false,true);

        $this->_megamenuConfig = $this->_helper->getConfig('sw_megamenu');

        $max_level = $this->_megamenuConfig['general']['max_level'];
        $html .= $this->getCustomBlockHtml('before');
        foreach ($categories as $category) {
            if (!$category->getIsActive()) {
                continue;
            }

            $cat_model = $this->getCategoryModel($category->getId());

            $sw_menu_hide_item = $cat_model->getData('sw_menu_hide_item');

            if(!$sw_menu_hide_item) {
                $children = $this->getActiveChildCategories($category);
                $sw_menu_cat_label = $cat_model->getData('sw_menu_cat_label');
                $sw_menu_icon_img = $cat_model->getData('sw_menu_icon_img');
                $sw_menu_font_icon = $cat_model->getData('sw_menu_font_icon');
                $sw_menu_cat_columns = $cat_model->getData('sw_menu_cat_columns');
                $sw_menu_float_type = $cat_model->getData('sw_menu_float_type');

                if(!$sw_menu_cat_columns){
                    $sw_menu_cat_columns = 4;
                }

                $menu_type = $cat_model->getData('sw_menu_type');
                if(!$menu_type)
                    $menu_type = $this->_megamenuConfig['general']['menu_type'];

                $custom_style = '';
                if($menu_type=="staticwidth")
                    $custom_style = ' style="width: 500px;"';

                $sw_menu_static_width = $cat_model->getData('sw_menu_static_width');
                if($menu_type=="staticwidth" && $sw_menu_static_width)
                    $custom_style = ' style="width: '.$sw_menu_static_width.';"';

                $item_class = 'level0 ';
                $item_class .= $menu_type.' ';

                $menu_top_content = $cat_model->getData('sw_menu_block_top_content');
                $menu_left_content = $cat_model->getData('sw_menu_block_left_content');
                $menu_left_width = $cat_model->getData('sw_menu_block_left_width');
                if(!$menu_left_content || !$menu_left_width)
                    $menu_left_width = 0;
                $menu_right_content = $cat_model->getData('sw_menu_block_right_content');
                $menu_right_width = $cat_model->getData('sw_menu_block_right_width');
                if(!$menu_right_content || !$menu_right_width)
                    $menu_right_width = 0;
                $menu_bottom_content = $cat_model->getData('sw_menu_block_bottom_content');
                if($sw_menu_float_type)
                    $sw_menu_float_type = 'fl-'.$sw_menu_float_type.' ';
                if(count($children) > 0 || (($menu_type=="fullwidth" || $menu_type=="staticwidth") && ($menu_top_content || $menu_left_content || $menu_right_content || $menu_bottom_content)))
                    $item_class .= 'parent ';
                $html .= '<ul class="mobile-megamenu-children submenu-cate-'.$category->getId().'" style="display: none">';
                $html .= '<li class="ui-menu-item '.$item_class.$sw_menu_float_type.'">';
                if(count($children) > 0) {
                    $html .= '<div class="open-children-toggle"></div>';
                }
                if ($sw_menu_icon_img && $cat_model->getLevel() != 2)
                    $html .= '<img class="menu-thumb-icon" src="' . $this->_helper->getBaseUrl().'' . $sw_menu_icon_img . '" alt="'.$category->getName().'"/>';
                elseif($sw_menu_font_icon)
                    $html .= '<em class="menu-thumb-icon '.$sw_menu_font_icon.'"></em>';
                if ($cat_model->getLevel() != 2) {
                    $html .= '<span>'.$category->getName().'</span>';
                }
                if($sw_menu_cat_label)
                    $html .= '<span class="cat-label cat-label-'.$sw_menu_cat_label.'">'.$this->_megamenuConfig['cat_labels'][$sw_menu_cat_label].'</span>';
                if(count($children) > 0 || (($menu_type=="fullwidth" || $menu_type=="staticwidth") && ($menu_top_content || $menu_left_content || $menu_right_content || $menu_bottom_content))) {
                    $subMenuClass = '';
                    $html .= '<div class="level0 submenu-mobile'.$subMenuClass.'"'.$custom_style.'>';
                    if(($menu_type=="fullwidth" || $menu_type=="staticwidth")) {
                        $html .= '<div class="container">';
                    }
                    if(($menu_type=="fullwidth" || $menu_type=="staticwidth") && $menu_top_content) {
                        $html .= '<div class="menu-top-block">'.$this->getBlockContent($menu_top_content).'</div>';
                    }
                    if(count($children) > 0 || (($menu_type=="fullwidth" || $menu_type=="staticwidth") && ($menu_left_content || $menu_right_content))) {
                        $html .= '<div class="row">';
                        if(($menu_type=="fullwidth" || $menu_type=="staticwidth") && $menu_left_content && $menu_left_width > 0) {
                            $html .= '<div class="menu-left-block col-md-'.$menu_left_width.'">'.$this->getBlockContent($menu_left_content).'</div>';
                        }
                        $html .= $this->getSubmenuItemsMobileHtml($children, 1, $max_level, 12-$menu_left_width-$menu_right_width, $menu_type, $sw_menu_cat_columns);
                        if(($menu_type=="fullwidth" || $menu_type=="staticwidth") && $menu_right_content && $menu_right_width > 0) {
                            $html .= '<div class="menu-right-block col-md-'.$menu_right_width.'">'.$this->getBlockContent($menu_right_content).'</div>';
                        }
                        $html .= '</div>';
                    }
                    if(($menu_type=="fullwidth" || $menu_type=="staticwidth") && $menu_bottom_content) {
                        $html .= '<div class="menu-bottom-block">'.$this->getBlockContent($menu_bottom_content).'</div>';
                    }
                    if(($menu_type=="fullwidth" || $menu_type=="staticwidth")) {
                        $html .= '</div>';
                    }
                    $html .= '</div>';
                }
                $html .= '</li>';
                $html .= '</ul>';
            }
        }

        return $html;
    }

    /**
     * @return string
     */
    public function getSubmenuLv1Html()
    {
        $html = '';

        $categories = $this->getStoreCategories(true,false,true);

        $this->_megamenuConfig = $this->_helper->getConfig('sw_megamenu');

        $max_level = $this->_megamenuConfig['general']['max_level'];
        $html .= $this->getCustomBlockHtml('before');
        foreach ($categories as $category) {
            if (!$category->getIsActive()) {
                continue;
            }

            $cat_model = $this->getCategoryModel($category->getId());

            $sw_menu_hide_item = $cat_model->getData('sw_menu_hide_item');

            if(!$sw_menu_hide_item) {
                $children = $this->getActiveChildCategories($category);
                $sw_menu_cat_columns = $cat_model->getData('sw_menu_cat_columns');
                $sw_menu_float_type = $cat_model->getData('sw_menu_float_type');

                if(!$sw_menu_cat_columns){
                    $sw_menu_cat_columns = 4;
                }

                $menu_type = $cat_model->getData('sw_menu_type');
                if(!$menu_type)
                    $menu_type = $this->_megamenuConfig['general']['menu_type'];

                $custom_style = '';
                if($menu_type=="staticwidth")
                    $custom_style = ' style="width: 500px;"';

                $sw_menu_static_width = $cat_model->getData('sw_menu_static_width');
                if($menu_type=="staticwidth" && $sw_menu_static_width)
                    $custom_style = ' style="width: '.$sw_menu_static_width.';"';

                $item_class = 'level0 ';
                $item_class .= $menu_type.' ';

                $menu_top_content = $cat_model->getData('sw_menu_block_top_content');
                $menu_left_content = $cat_model->getData('sw_menu_block_left_content');
                $menu_left_width = $cat_model->getData('sw_menu_block_left_width');
                if(!$menu_left_content || !$menu_left_width)
                    $menu_left_width = 0;
                $menu_right_content = $cat_model->getData('sw_menu_block_right_content');
                $menu_right_width = $cat_model->getData('sw_menu_block_right_width');
                if(!$menu_right_content || !$menu_right_width)
                    $menu_right_width = 0;
                $menu_bottom_content = $cat_model->getData('sw_menu_block_bottom_content');
                if($sw_menu_float_type)
                    $sw_menu_float_type = 'fl-'.$sw_menu_float_type.' ';
                if(count($children) > 0 || (($menu_type=="fullwidth" || $menu_type=="staticwidth") && ($menu_top_content || $menu_left_content || $menu_right_content || $menu_bottom_content)))
                    $item_class .= 'parent ';
                if(count($children) > 0 || (($menu_type=="fullwidth" || $menu_type=="staticwidth") && ($menu_top_content || $menu_left_content || $menu_right_content || $menu_bottom_content))) {
                    $subMenuClass = '';
                    if(count($children) > 0 || (($menu_type=="fullwidth" || $menu_type=="staticwidth") && ($menu_left_content || $menu_right_content))) {
                        $html .= '<div class="row">';
                        $html .= $this->getSubmenuItemsMobileHtmlLv1($children, 1, $max_level, 12-$menu_left_width-$menu_right_width, $menu_type, $sw_menu_cat_columns);
                        $html .= '</div>';
                    }
                }
            }
        }

        return $html;
    }

    /**
     * @param $children
     * @param int $level
     * @param int $max_level
     * @param int $column_width
     * @param string $menu_type
     * @param null $columns
     * @return string
     */
    public function getSubmenuItemsMobileHtmlLv1($children, $level = 1, $max_level = 0, $column_width=12, $menu_type = 'fullwidth', $columns = null)
    {
        $html = '';

        if(!$max_level || ($max_level && $max_level == 0) || ($max_level && $max_level > 0 && $max_level-1 >= $level)) {
            $column_class = "";
            if($level == 1 && $columns && ($menu_type == 'fullwidth' || $menu_type == 'staticwidth')) {
                $column_class .= "mega-columns columns".$columns;
            }
            foreach($children as $child) {
                $cat_model = $this->getCategoryModel($child->getId());

                $sw_menu_hide_item = $cat_model->getData('sw_menu_hide_item');

                if (!$sw_menu_hide_item) {
                    $sub_children = $this->getActiveChildCategories($child);

                    $item_class = 'level'.$level.' ';
                    if(count($sub_children) > 0)
                        $item_class .= 'parent ';
                    if(count($sub_children) > 0) {
                        $html .= $this->getSubmenuItemsChidHtml($sub_children, $level+1, $max_level, $column_width, $menu_type, null, $child);
                    }
                }
            }
        }

        return $html;
    }

    /**
     * @param $children
     * @param int $level
     * @param int $max_level
     * @param int $column_width
     * @param string $menu_type
     * @param null $columns
     * @param null $parent
     * @return string
     */
    public function getSubmenuItemsChidHtml($children, $level = 1, $max_level = 0, $column_width=12, $menu_type = 'fullwidth', $columns = null, $parent = null)
    {
        $html = '';

        if(!$max_level || ($max_level && $max_level == 0) || ($max_level && $max_level > 0 && $max_level-1 >= $level)) {
            $column_class = "";
            if($level == 1 && $columns && ($menu_type == 'fullwidth' || $menu_type == 'staticwidth')) {
                $column_class .= "mega-columns columns".$columns;
            }
            $html = '<ul class="subchildmenu '.$column_class.' subchildmenu-cate-'.$parent->getId().'" style="display: none">';
            $html .= '<div class="megamenu-mobile-navigation">';
            $html .= '<span class="megamenu-mobile-navigation-left"></span>';
            $html .= '<span class="megamenu-mobile-navigation-label">'.$parent->getName().'</span>';
            $html .= '</div>';
            $html .= '<div class="menu-items-lv2-content">';
            foreach($children as $child) {
                $cat_model = $this->getCategoryModel($child->getId());

                $sw_menu_hide_item = $cat_model->getData('sw_menu_hide_item');

                if (!$sw_menu_hide_item) {
                    $sub_children = $this->getActiveChildCategories($child);

                    $sw_menu_cat_label = $cat_model->getData('sw_menu_cat_label');
                    $sw_menu_icon_img = $cat_model->getData('sw_menu_icon_img');
                    $sw_menu_font_icon = $cat_model->getData('sw_menu_font_icon');

                    $item_class = 'level'.$level.' ';
                    if(count($sub_children) > 0)
                        $item_class .= 'parent ';
                    $html .= '<li class="ui-menu-item '.$item_class.'">';
                    if(count($sub_children) > 0) {
                        $html .= '<div class="open-children-toggle"></div>';
                    }
                    if($level == 0 && $sw_menu_icon_img) {
                        $html .= '<div class="menu-thumb-img"><a class="menu-thumb-link" href="'.$this->_categoryHelper->getCategoryUrl($child).'"><img src="' . $this->_helper->getBaseUrl().'' . $sw_menu_icon_img . '" alt="'.$child->getName().'"/></a></div>';
                    }
                    $html .= '<a href="'.$this->_categoryHelper->getCategoryUrl($child).'" title="'.$child->getName().'">';
                    if ($level >= 1 && $sw_menu_icon_img)
                        $html .= '<img class="menu-thumb-icon" src="' . $this->_helper->getBaseUrl().'' . $sw_menu_icon_img . '" alt="'.$child->getName().'"/>';
                    elseif($sw_menu_font_icon)
                        $html .= '<em class="menu-thumb-icon '.$sw_menu_font_icon.'"></em>';
                    $html .= '<span>'.$child->getName();
                    if($sw_menu_cat_label)
                        $html .= '<span class="cat-label cat-label-'.$sw_menu_cat_label.'">'.$this->_megamenuConfig['cat_labels'][$sw_menu_cat_label].'</span>';
                    $html .= '</span></a>';
                    if(count($sub_children) > 0) {
                        $html .= $this->getSubmenuItemsHtml($sub_children, $level+1, $max_level, $column_width, $menu_type);
                    }
                    $html .= '</li>';
                }
            }
            $html .= '</div>';
            $html .= '</ul>';
        }

        return $html;
    }

    /**
     * @param $children
     * @param int $level
     * @param int $max_level
     * @param int $column_width
     * @param string $menu_type
     * @param null $columns
     * @return string
     */
    public function getSubmenuItemsMobileHtml($children, $level = 1, $max_level = 0, $column_width=12, $menu_type = 'fullwidth', $columns = null)
    {
        $html = '';

        if(!$max_level || ($max_level && $max_level == 0) || ($max_level && $max_level > 0 && $max_level-1 >= $level)) {
            $column_class = "";
            if($level == 1 && $columns && ($menu_type == 'fullwidth' || $menu_type == 'staticwidth')) {
                $column_class .= "mega-columns columns".$columns;
            }
            $html = '<ul class="subchildmenu '.$column_class.'">';
            foreach($children as $child) {
                $cat_model = $this->getCategoryModel($child->getId());

                $sw_menu_hide_item = $cat_model->getData('sw_menu_hide_item');

                if (!$sw_menu_hide_item) {
                    $sub_children = $this->getActiveChildCategories($child);

                    $sw_menu_cat_label = $cat_model->getData('sw_menu_cat_label');
                    $sw_menu_icon_img = $cat_model->getData('sw_menu_icon_img');
                    $sw_menu_font_icon = $cat_model->getData('sw_menu_font_icon');

                    $item_class = 'level'.$level.' ';
                    if(count($sub_children) > 0)
                        $item_class .= 'parent ';
                    $html .= '<li class="ui-menu-item '.$item_class.'">';
                    if(count($sub_children) > 0) {
                        $html .= '<div class="open-children-toggle" data-cateid="'.$child->getId().'"></div>';
                    }
                    if($level == 0 && $sw_menu_icon_img) {
                        $html .= '<div class="menu-thumb-img"><a class="menu-thumb-link" href="'.$this->_categoryHelper->getCategoryUrl($child).'"><img src="' . $this->_helper->getBaseUrl().'' . $sw_menu_icon_img . '" alt="'.$child->getName().'"/></a></div>';
                    }
                    $html .= '<a href="'.$this->_categoryHelper->getCategoryUrl($child).'" title="'.$child->getName().'">';
                    if ($level >= 1 && $sw_menu_icon_img)
                        $html .= '<img class="menu-thumb-icon" src="' . $this->_helper->getBaseUrl().'' . $sw_menu_icon_img . '" alt="'.$child->getName().'"/>';
                    elseif($sw_menu_font_icon)
                        $html .= '<em class="menu-thumb-icon '.$sw_menu_font_icon.'"></em>';
                    $html .= '<span>'.$child->getName();
                    if($sw_menu_cat_label)
                        $html .= '<span class="cat-label cat-label-'.$sw_menu_cat_label.'">'.$this->_megamenuConfig['cat_labels'][$sw_menu_cat_label].'</span>';
                    $html .= '</span></a>';
                    $html .= '</li>';
                }
            }
            $html .= '</ul>';
        }

        return $html;
    }
}
