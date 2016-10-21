<?php
/******************************
 * $File:CommonTree.class.php
 * $Description: 通用树形结构类(无限分类)
 * $Author: chenwei 
 * $Time:2013-12-11
 * $Update:chenwei
 * $UpdateDate:2013-12-11
******************************/
class CommonTree
{
    
    // 分类的名称
    public $data = array();
    
    // 分类的其他信息
    public $info = array();

    public $item = array();

    public $cateArray = array();
    
    // 控制状态(是否显示)的键
    public $statusField = 'categories_status';

    public $parentIdField = 'parent_id';
    
    // 下拉菜单不同级别的前缀符号。
    public $preStr = '&nbsp;&nbsp;';

    function __construct()
    {}

    function setNode($id, $parent, $value, $info = array())
    {
        $parent = $parent ? $parent : 0;
        
        $this->data[$id] = $value;
        
        if (! isset($info[$this->statusField])) {
            
            $info[$this->statusField] = 1;
        }
        
        $this->info[$id] = $info;
        
        $this->cateArray[$id] = $parent;
    }

    function getChildsTree($id = 0)
    {
        $childs = array();
        
        foreach ($this->cateArray as $child => $parent) {
            
            if ($parent == $id) {
                
                $childs[$child] = $this->getChildsTree($child);
            }
        }
        
        return $childs;
    }

    function getChilds($id = 0)
    {
        $childArray = array();
        
        $childs = $this->getChild($id);
        
        // 包含当前节点。
        
        if ($id != 0)
            $childArray[$id] = $id;
        
        foreach ($childs as $child) {
            
            $childArray[] = $child;
            
            $childArray = array_merge($childArray, $this->getChilds($child));
        }
        
        // $this->item['']
        
        return $childArray;
    }
    
    // 返回某个$id子分类下的数据,0是根分类
    
    // $selected是选中的编号
    
    // $return_array是否返回数组，如果不是则返回字符串<option value="......">......</option>
    
    // level 保留多少级 0 表示不限制
    
    // $is_show_all是否显示所有子分类
    function getAllData($id, $selected, $return_array = TRUE, $level = 0, $is_show_all = TRUE)
    {
        $result = $this->getChilds($id);
        
        if (count($result) > 0) {
            
            foreach ($result as $v) {
                
                $this->item[$v]['id'] = $v;
                
                $this->item[$v]['data'] = $this->getValue($v);
                
                $this->item[$v]['level'] = count($this->getNodeLever($v));
                
                $this->item[$v]['has_children'] = count($this->getChildsTree($v));
                
                if (isset($this->info[$v][$this->parentIdField])) {
                    
                    $this->item[$v][$this->parentIdField] = $this->info[$v][$this->parentIdField];
                }
                
                $this->item[$v]['info'] = $this->info[$v];
            }
            
            $children_level = 99999; // 大于这个分类的将被删除
            
            if ($is_show_all == FALSE) {
                
                foreach ($this->item as $key => $it) {
                    
                    if ($it['level'] > $children_level) 

                    {
                        
                        unset($this->item[$key]);
                    } else {
                        
                        // 如果不全部显示，跳过不显示的元素
                        
                        if (! $it['info'][$this->statusField]) {
                            
                            unset($this->item[$key]);
                            
                            if ($children_level > $it['level']) 

                            {
                                
                                $children_level = $it['level']; // 标记一下，这样子分类也能删除
                            }
                        } else {
                            
                            $children_level = 99999; // 恢复初始值
                        }
                    }
                }
            }
            
            /* 截取到指定的缩减级别 */
            
            if ($level > 0) 

            {
                
                if ($id == 0) 

                {
                    
                    $end_level = $level;
                } 

                else 

                {
                    
                    $first_item = reset($this->item); // 获取第一个元素
                    
                    $end_level = $first_item['level'] + $level;
                }
                
                /* 保留level小于end_level的部分 */
                
                foreach ($this->item as $key => $val) 

                {
                    
                    if ($val['level'] >= $end_level) 

                    {
                        
                        unset($this->item[$key]);
                    }
                }
            }
            
            if ($return_array) {
                
                return $this->item;
            } else {
                
                $str = '';
                
                foreach ($this->item as $v) {
                    
                    if ($v['id'] != $selected) {
                        
                        $str .= '<option value="' . $v['id'] . '">' . str_repeat($this->preStr, intval($v['level'])) . $v['data'] . '</option>' . "\n";
                    } else {
                        
                        $str .= '<option value="' . $v['id'] . '" selected="selected">' . str_repeat($this->preStr, intval($v['level'])) . $v['data'] . '</option>' . "\n";
                    }
                }
                
                return $str;
            }
        } else {
            
            return false;
        }
    }

    function getChild($id)
    {
        $childs = array();
        
        foreach ($this->cateArray as $child => $parent) {
            
            if ($parent == $id) {
                
                $childs[$child] = $child;
            }
        }
        
        return $childs;
    }
    
    // 单线获取父节点
    function getNodeLever($id)
    {
        $parents = array();
        
        if (key_exists($this->cateArray[$id], $this->cateArray)) {
            
            $parents[] = $this->cateArray[$id];
            
            $parents = array_merge($parents, $this->getNodeLever($this->cateArray[$id]));
        }
        
        return $parents;
    }
    
    // 返回带缩进格式的字符串
    function getLayer($id)
    {
        return str_repeat($this->preStr, count($this->getNodeLever($id)));
    }
    
    // 获取分类名称
    function getValue($id)
    {
        return $this->data[$id];
    }
    
    /*
     * 单独的方法用来格式化children例如:
     Array
    (
        [1] => Array
        (
            [id] => 1
            [data] => 台历
            [level] => 0
            [has_children] => 2
            [parent_id] => 0
            [info] => Array
            (
            [has_children] => 2
            [parent_id] => 0
            [status] => 1
            [sort_order] => 500
            )

            [children] => Array
            (
                [0] => Array
                (
                    [id] => 4
                    [data] => 大台历
                    [level] => 1
                    [has_children] => 0
                    [parent_id] => 1
                    [info] => Array
                    (
                        [has_children] => 0
                        [parent_id] => 1
                        [status] => 1
                        [sort_order] => 500
                    )

                )

                [1] => Array
                (
                    [id] => 3
                    [data] => 小台历
                    [level] => 1
                    [has_children] => 0
                    [parent_id] => 1
                    [info] => Array
                    (
                        [has_children] => 0
                        [parent_id] => 1
                        [status] => 1
                        [sort_order] => 500
                    )

                )

            )

        )

    )
     */
    function genChildren($items, $id = 'id', $pid = 'parent_id', $son = 'children')
    {
        $tree = array(); // 格式化的树
        
        $tmpMap = array(); // 临时扁平数据
        
        if (empty($items))
            return $tree;
        
        foreach ($items as $item) {
            
            $tmpMap[$item[$id]] = $item;
        }
        
        foreach ($items as $item) {
            
            if (isset($tmpMap[$item[$pid]])) {
                
                $tmpMap[$item[$pid]][$son][$item[$id]] = &$tmpMap[$item[$id]];
            } else {
                
                $tree[$item[$id]] = &$tmpMap[$item[$id]];
            }
        }
        
        unset($tmpMap);
        
        return $tree;
    }
}

?>