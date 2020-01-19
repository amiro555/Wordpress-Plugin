<?php

/*
Plugin Name: Post Analysis
Plugin URI: -
Description: Post Analysis
Author: Amiro Phiwsawang
Author URI: -
Version: 1.1
*/

$file = ABSPATH."/wp-content/plugins/AnalysisPost/display.php";    
require( $file );

?><style>
    .btnAnalysis {
          background-color: #4CAF50;
          border: none;
          color: white;
          padding: 8px 32px;
          text-decoration: none;
          margin: 10px 3px;
          cursor: pointer;
          font-size: 18pt;
        }
</style><?php

function add_analysis_menu() {
    add_menu_page(
        __('InfoAnalytics', 'textdomain' ),
           'InfoAnalytics',
           'manage_options',
           'InfoAnalytics',
           'call_post_analytics',
            'dashicons-search',
            2
        );
    }

add_action('admin_menu', 'add_analysis_menu');


function call_post_analytics()
{
    echo '<form method = post>';
    echo "<br><input type = 'submit' name = 'btnAnalysis' class='btnAnalysis' value ='Analytics' />";
    echo '</form>';

    if (!empty($_POST))
    {
        global $wpdb;
        $recent_posts = wp_get_recent_posts(array(
            'numberposts' => 50,
            'post_status' => 'publish'
        ));
        foreach($recent_posts as $post) :  

            $post_url = get_permalink($post['ID']);
            // if($post_url != strpos($post_url, 'localhost')){
                $start = strpos($post_url, 'wordpress');
                $post_url = substr($post_url, ($start + 9), -1);
                $post_url = 'https://www.infoquest.co.th'.$post_url;
            // }
            
            $analysis_link = 'https://graph.facebook.com/?id='.$post_url.'&fields=og_object{engagement}';
            $get_data = file_get_contents($analysis_link);
            $json_data = json_decode($get_data);
            $share_count = $json_data->og_object->engagement->count;

            // echo '<li>(id:'.$post['ID'].') <a href="'.$post_url.'">'.$post['post_title'].'</a> share:'.$share_count.'</li>';
            $insert = $wpdb->query("INSERT INTO post_analysis (post_id,url,share,dateTime) VALUES (".$post['ID'].",'$post_url',$share_count,NOW() )");

            $success;
            $faild;
            if ($insert) {
                    echo "<h3>inject successfuly(".$post['ID'].")</h3>";
                    $success += 1;
            } else {
                    echo "<h3>inject faild(".$post['ID'].")</h3>";
                    $faild += 1;
            }
            unset($insert);
            unset($get_data);
            unset($json_data);
            sleep(2);
        endforeach; wp_reset_query();
        echo "Complete : ".$success." Failed : ".$faild;
    }
    unset($recent_posts);
}

?>