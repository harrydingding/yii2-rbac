<?PHP
/**
  *  
  *  @author Harry Ding <harry.402@hotmail.com>
  *
 **/
namespace harrydingding\rbac\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Menu;

class TreeMenu extends Menu
{   
    public $urlItemCssClass = "nav nav-tabs nav-stacked main-menu";
    
    /**
     * @var string the template used to render the body of a menu which is a link.
     * In this template, the token `{url}` will be replaced with the corresponding link URL;
     * while `{label}` will be replaced with the link text.
     * This property will be overridden by the `template` option set in individual menu items via [[items]].
     */
    public $linkTemplate = '<a href="{url}"><i class="{icon}"></i>{label}</a>';
    /**
     * @var string the template used to render the body of a menu which is NOT a link.
     * In this template, the token `{label}` will be replaced with the label of the menu item.
     * This property will be overridden by the `template` option set in individual menu items via [[items]].
     */
    public $labelTemplate = '<a href="#" class="dropdown-toggle" data-toggle="dropdown" ><i class="{icon}"></i>{label}<b class="fa fa-plus dropdown-plus"></b></a>';
    /**
     * @var string the template used to render a list of sub-menus.
     * In this template, the token `{items}` will be replaced with the rendered sub-menu items.
     */
    public $sublabelTemplate = '<a href="#" class="dropdown-toggle" data-toggle="dropdown" ><i class="fa fa-caret-right"></i>{label}</a>';
    
    
    public $submenuTemplate = "\n<ul class='dropdown-menu' >\n{items}\n</ul>\n";
    
    public $submenuLableTemplate = "\n<ul class='dropdown-submenu' >\n{items}\n</ul>\n";
    
    
    /**
     * clean up the menu list to the tree
     * @param Menu $list
     * @return array | clean up the menu list
     */
    public static function nav($list)
    {
        $tree = array();
        $packData = array();
        
        foreach ($list as $data) {
            $packData[$data['id']] = $data;
        }
        foreach ($packData as $key =>$val){
            if($val['parent_id'] == 0){
                $tree['items'][]= &$packData[$key];
            }else{
                $packData[$val['parent_id']]['items'][]= &$packData[$key];
            }
        }
        return $tree;
    }
    
    
    public static function widget($model)
    {
        ob_start();
        ob_implicit_flush(false);
        try {
            $config = self::nav($model);
//             print_r($config);die;
            $config['options'] = ['class' => 'menu'];
            /* @var $widget Widget */
            $config['class'] = get_called_class();
            $widget = Yii::createObject($config);
            $out = '';
            if ($widget->beforeRun()) {
                $result = $widget->run();
                $out = $widget->afterRun($result);
            }
        } catch (\Exception $e) {
            // close the output buffer opened above if it has not been closed already
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            throw $e;
        }
        
        return ob_get_clean() . $out;
    }
    
    /**
     * Recursively renders the menu items (without the container tag).
     * @param array $items the menu items to be rendered recursively
     * @return string the rendering result
     */
    protected function renderItems($items)
    {
        $n = count($items);
        $lines = [];
        foreach ($items as $i => $item) {
            $options = array_merge($this->itemOptions, ArrayHelper::getValue($item, 'options', []));
            $tag = ArrayHelper::remove($options, 'tag', 'li');
            $class = [];
            if ($item['active']) {
                $class[] = $this->activeCssClass;
            }
            if ($i === 0 && $this->firstItemCssClass !== null) {
                $class[] = $this->firstItemCssClass;
            }
            if ($i === $n - 1 && $this->lastItemCssClass !== null) {
                $class[] = $this->lastItemCssClass;
            }
            if (!isset($item['url'])) {
                $class[] = $this->urlItemCssClass;
            }
            Html::addCssClass($options, $class);
            
            $nav = $this->renderItem($item);
            if (!empty($item['items'])) {
                if ($item['sub_menu'] == true) {
                    $submenuLableTemplate = ArrayHelper::getValue($item, 'submenuLableTemplate', $this->submenuLableTemplate);
                    $nav .= strtr($submenuLableTemplate, [
                        '{items}' => $this->renderItems($item['items']),
                    ]);
                } else {
                    $submenuTemplate = ArrayHelper::getValue($item, 'submenuTemplate', $this->submenuTemplate);
                    $nav .= strtr($submenuTemplate, [
                        '{items}' => $this->renderItems($item['items']),
                    ]);
                }
                
            }
            $lines[] = Html::tag($tag, $nav, $options);
        }
        return implode("\n", $lines);
    }
    
    /**
     * Renders the content of a menu item.
     * Note that the container and the sub-menus are not rendered here.
     * @param array $item the menu item to be rendered. Please refer to [[items]] to see what data might be in the item.
     * @return string the rendering result
     */
    protected function renderItem($item)
    {
        if (isset($item['url'])) {
            $template = ArrayHelper::getValue($item, 'linkTemplate', $this->linkTemplate);
            
            return strtr($template, [
                '{url}' => Html::encode(Url::toRoute($item['url'])),
                '{label}' => $item['label'],
                '{icon}' => $item['icon'],
            ]);
        } elseif (!isset($item['url']) && $item['sub_menu'] == true) {
            $template = ArrayHelper::getValue($item, 'sublabelTemplate', $this->sublabelTemplate);
            
            return strtr($template, [
                '{label}' => $item['label'],
            ]);
        } else {
            $template = ArrayHelper::getValue($item, 'labelTemplate', $this->labelTemplate);
            
            return strtr($template, [
                '{label}' => $item['label'],
                '{icon}' => $item['icon'],
            ]);
        }
    }
    
    
}
