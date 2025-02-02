<?php

declare(strict_types = 1);

namespace Weiran\Framework\Helper;

/**
 * 通用的树型类，可以生成任何树型结构
 */
class TreeHelper
{
    /**
     * 生成树型结构所需要的2维数组
     * @var array
     */
    public $arr = [];

    /**
     * Tree
     * @var array
     */
    public $tree = [];

    /**
     * ID
     * @var int
     */
    private $keyId;

    /**
     * PID
     * @var int
     */
    private $keyPid;

    /**
     * Title
     * @var string
     */
    private $keyTitle;

    /**
     * 生成树型结构所需修饰符号，可以换成图片
     * @var array
     */
    public $icon = ['&nbsp;│', '&nbsp;├', '&nbsp;└'];

    /**
     * Space
     * @var string
     */
    public $space = '&nbsp;';

    /**
     * @access private
     */
    public $ret = '';

    /**
     * 构造函数，初始化类
     * @param array  $arr     2维数组，例如：
     *                        array(
     *                        1 => array('id'=>'1','pid'=>0,'name'=>'一级栏目一'),
     *                        2 => array('id'=>'2','pid'=>0,'name'=>'一级栏目二'),
     *                        3 => array('id'=>'3','pid'=>1,'name'=>'二级栏目一'),
     *                        4 => array('id'=>'4','pid'=>1,'name'=>'二级栏目二'),
     *                        5 => array('id'=>'5','pid'=>2,'name'=>'二级栏目三'),
     *                        6 => array('id'=>'6','pid'=>3,'name'=>'三级栏目一'),
     *                        7 => array('id'=>'7','pid'=>3,'name'=>'三级栏目二')
     *                        )
     * @param string $k_id    id key
     * @param string $k_pid   pid key
     * @param string $k_title title key
     * @return bool
     */
    public function init($arr = [], $k_id = 'id', $k_pid = 'pid', $k_title = 'name')
    {
        $this->arr      = $arr;
        $this->ret      = '';
        $this->keyId    = $k_id;
        $this->keyPid   = $k_pid;
        $this->keyTitle = $k_title;

        return is_array($arr);
    }

    /**
     * 空格替换
     */
    public function replaceSpace(): void
    {
        $this->icon  = [' │', ' ├', ' └'];
        $this->space = ' ';
    }

    /**
     * 得到父级数组
     * @param int id id
     * @return array|bool
     */
    public function getParent($id)
    {
        $newArray = [];
        if (!isset($this->arr[$id])) {
            return false;
        }
        $pid = $this->arr[$id][$this->keyPid];
        $pid = $this->arr[$pid][$this->keyPid];
        if (is_array($this->arr)) {
            foreach ($this->arr as $kid => $a) {
                if ((int) $a[$this->keyPid] === $pid) {
                    $newArray[$kid] = $a;
                }
            }
        }

        return $newArray;
    }

    /**
     * 得到子级数组
     * @param int
     * @return array|bool
     */
    public function getChild($id)
    {
        $newArray = [];
        if (is_array($this->arr)) {
            foreach ($this->arr as $kid => $a) {
                if ((int) $a[$this->keyPid] === $id) {
                    $newArray[$kid] = $a;
                }
            }
        }

        return $newArray ?: false;
    }

    /**
     * 得到当前位置数组
     * @param int   $id       id
     * @param array $newArray newArray
     * @return array|bool
     */
    public function getPos($id, &$newArray)
    {
        $a = [];
        if (!isset($this->arr[$id])) {
            return false;
        }
        $newArray[] = $this->arr[$id];
        $pid        = $this->arr[$id][$this->keyPid];
        if (isset($this->arr[$pid])) {
            $this->getPos($pid, $newArray);
        }
        if (is_array($newArray)) {
            krsort($newArray);
            foreach ($newArray as $v) {
                $a[$v[$this->keyId]] = $v;
            }
        }

        return $a;
    }

    /**
     * 得到树型结构
     * @param int    $my_id       ID，表示获得这个ID下的所有子级
     * @param string $str         生成树型结构的基本代码，例如："<option value=\$id \$selected>\$spacer\$name</option>"
     * @param int    $selected_id 被选中的ID，比如在做树型下拉框的时候需要用到
     * @param string $adds        是否添加指示标志
     * @param string $str_group   分组
     * @return string
     */
    public function getTree($my_id, $str, $selected_id = 0, $adds = '', $str_group = ''): string
    {
        $number   = 1;
        $children = $this->getChild($my_id);
        if (is_array($children)) {
            $total = count($children);
            foreach ($children as $node_id => $node) {
                $j = $k = '';
                if ($number == $total) {
                    $j .= $this->icon[2];
                }
                else {
                    $j .= $this->icon[1];
                    $k = $adds ? $this->icon[0] : '';
                }

                $spacer   = $adds ? $adds . $j : '';
                $selected = $node_id == $selected_id ? 'selected="selected"' : '';
                @extract($node);
                $nstr = '';
                if ($node[$this->keyPid] == 0 && isset($node['str_group'])) {
                    eval("\$nstr = \"$str_group\";");
                }
                else {
                    eval("\$nstr = \"$str\";");
                }
                $this->ret .= $nstr;
                $nbsp      = $this->space;
                $this->getTree($node_id, $str, $selected_id, $adds . $k . $nbsp, $str_group);
                $number++;
            }
        }

        return $this->ret;
    }

    /**
     * 获取树数组
     * @param int    $id   id
     * @param string $adds 追加
     * @param string $type 类型
     * @return array
     */
    public function getTreeArray($id, $adds = '', $type = 'default')
    {
        $number   = 1;
        $children = $this->getChild($id);
        if (is_array($children)) {
            $total = count($children);
            foreach ($children as $node_id => $node) {
                $j = $k = '';
                if ($number == $total) {
                    $j .= $this->icon[2];
                }
                else {
                    $j .= $this->icon[1];
                    $k = $adds ? $this->icon[0] : '';
                }
                $spacer                          = $adds ? $adds . $j : '';
                $this->tree[$node[$this->keyId]] = $spacer . $node[$this->keyTitle];
                $nbsp                            = $this->space;
                $this->getTreeArray($node_id, $adds . $k . $nbsp);
                $number++;
            }
        }
        if ($type == 'default') {
            return $this->tree;
        }

        $tree = [];
        foreach ($this->tree as $key => $value) {
            $tree[] = [
                'key'   => $key,
                'value' => $value,
            ];
        }

        return $tree;
    }

    /**
     * 同上一方法类似,但允许多选
     * @param int    $myid id
     * @param string $str  字串
     * @param int    $sid  id
     * @param string $adds 附加信息
     * @return string
     */
    public function getTreeMulti($myid, $str, $sid = 0, $adds = '')
    {
        $number = 1;
        $child  = $this->getChild($myid);
        if (is_array($child)) {
            $total = count($child);
            foreach ($child as $kid => $a) {
                $j = $k = '';
                if ($number == $total) {
                    $j .= $this->icon[2];
                }
                else {
                    $j .= $this->icon[1];
                    $k = $adds ? $this->icon[0] : '';
                }
                $spacer = $adds ? $adds . $j : '';

                $selected = $this->has($sid, $kid) ? 'selected' : '';
                @extract($a);
                $nstr = '';
                eval("\$nstr = \"$str\";");
                $this->ret .= $nstr;
                $this->getTreeMulti($kid, $str, $sid, $adds . $k . '&nbsp;');
                $number++;
            }
        }

        return $this->ret;
    }

    /**
     * 树分类
     * @param int    $myid 要查询的ID
     * @param string $str  第一种HTML代码方式
     * @param string $str2 第二种HTML代码方式
     * @param int    $sid  默认选中
     * @param string $adds 前缀
     * @return string
     */
    public function getTreeCategory($myid, $str, $str2, $sid = 0, $adds = '')
    {
        $number = 1;
        $child  = $this->getChild($myid);
        if (is_array($child)) {
            $total = count($child);
            foreach ($child as $id => $a) {
                $j = $k = '';
                if ($number == $total) {
                    $j .= $this->icon[2];
                }
                else {
                    $j .= $this->icon[1];
                    $k = $adds ? $this->icon[0] : '';
                }
                $spacer   = $adds ? $adds . $j : '';
                $selected = $this->has($sid, $id) ? 'selected' : '';
                @extract($a);
                $nstr = '';
                if (empty($html_disabled)) {
                    eval("\$nstr = \"$str\";");
                }
                else {
                    eval("\$nstr = \"$str2\";");
                }
                $this->ret .= $nstr;
                $this->getTreeCategory($id, $str, $str2, $sid, $adds . $k . '&nbsp;');
                $number++;
            }
        }

        return $this->ret;
    }

    /**
     * 是否存在
     * @param string $list 位置
     * @param string $item 条目
     * @return bool|int
     */
    private function has($list, $item)
    {
        return strpos(',,' . $list . ',', ',' . $item . ',');
    }
}