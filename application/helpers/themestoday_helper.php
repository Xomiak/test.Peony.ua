<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function showTheme($category)
{    
    $CI = & get_instance();
    $CI->db->where('name','server_name');
    $server_name = $CI->db->get('options')->result_array();
    if($server_name) $server_name = $server_name[0]['value'];
    $CI->db->where('name',$category);
    $cat = $CI->db->get('categories')->result_array();
    $parent = '';
    if($cat[0]['parent'] != 0)
    {
        $CI->db->where('id',$cat[0]['parent']);
        $parent = $CI->db->get('categories')->result_array();
        $parent = $parent[0];
    }
?>
    <table width="100%"  cellpadding="0" cellspacing="0">                                       
        <tr>
            <td align="center"><h4><a href="<?php if(isset($parent['url'])) echo '/'.$parent['url']; ?>/<?=$cat[0]['url']?>/" style="color: #CC0000;"><?=$category?></a></h4></td>
        </tr>
        <tr>
            <td align="center">
                <?php
                
                if($cat){
                    $cat = $cat[0];
                    $query = "* FROM articles WHERE";
                    $query .= " active=1 AND";
                    $query .= "(category_id=".$cat['id']." or category_id like \"%*".$cat['id']."\" or category_id like \"%*".$cat['id']."*%\" or category_id like \"".$cat['id']."*%\")";
                    $query .= " ORDER BY num DESC LIMIT 1";
                    $CI->db->select($query, FALSE);
                    //$CI->db->where('category_id',$cat['id']);
                    //$CI->db->order_by('num','DESC');
                    //$CI->db->limit(1);
                    $art = $CI->db->get()->result_array();
                    if($art){
                        $art = $art[0];
                        $category = $cat;
                        if($cat['parent'] != 0)
                        {
                            $CI->db->where('id',$cat['parent']);
                            $cat = $CI->db->get('categories')->result_array();
                            if($cat) $cat = $cat[0];                            
                        }                                                        
                        if($art['image'] != '')
                        {
                            echo '<a href="/';
                            if($parent) echo $parent['url'].'/';
                            echo $category['url'].'/'.$art['url'].'/">
                                    <img src="'.$server_name.$art['image'].'" alt="'.$art['name'].'" title="'.$art['name'].'" width="178px" />
                                  </a>';
                        }
                    }
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php
                if($art)
                {
                    echo '<h5><a href="/';
                    if($parent) echo $parent['url'].'/';
                    echo $category['url'].'/'.$art['url'].'/">
                                    '.$art['name'].'
                                  </a></h5>';
                }
                ?>
            </td>
        </tr>
    </table>
<?php
}
?>