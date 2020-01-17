<?php

/*
Plugin Name: Post Analysis(New)
Plugin URI: -
Description: Craw News
Author: Amiro Phiwsawang
Author URI: -
Version: 1.00
*/

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
            '',
            2
        );
    }

add_action('admin_menu', 'add_analysis_menu');


function call_post_analytics()
{
    echo '<form method = post>';
    echo "<br><input type = 'submit' name = 'btnAnalysis' id ='btnAnalysis' class='btnAnalysis' value ='Analytics' />";
    echo '</form>';

    if (!empty($_POST))
    {

    ?><script type="text/javascript">
      doc      
    </script> <?php

    global $wpdb;
    $recent_posts = wp_get_recent_posts(array(
        'numberposts' => 50,
        'post_status' => 'publish'
    ));
    foreach($recent_posts as $post) :    

       $post_url = get_permalink($post['ID']);
       $start = strpos($post_url, 'mysite');
       $post_url = substr($post_url, ($start + 6), -1);
       $post_url = 'https://www.infoquest.co.th'.$post_url;
       $analysis_link = 'https://graph.facebook.com/?id='.$post_url.'&fields=og_object{engagement}';
       $get_data = file_get_contents($analysis_link);
       $json_data = json_decode($get_data);
       $share_count = $json_data->og_object->engagement->count;

       // echo '<li>(id:'.$post['ID'].') <a href="'.$post_url.'">'.$post['post_title'].'</a> share:'.$share_count.'</li>';
       $insert = $wpdb->query("INSERT INTO wp_analysis (post_id,url,share,dateTime) VALUES (".$post['ID'].",'$post_url',$share_count,NOW() )");

       $success;
       $faild;
       if ($insert) {
            echo "<h3>inject successfuly(".$post['ID'].")</h3>";
            $success += 1;
       } else {
            echo "<h3>inject faild(".$post['ID'].")</h3>";
            $faild += 1;
       }
       sleep(1);
    endforeach; wp_reset_query();
    echo "Complete : ".$success." Failed : ".$faild;
    }
}

function show_top_share()
{
    
    global $wpdb;
    $selectTopNews = $wpdb->get_results("SELECT post_id , share , sum(share) as totalshared
                        FROM wp_analysis Group by post_id
                        Order by totalshared desc limit 10");


    echo "<h3>TOP NEWS</h3>";
    for ($i = 0; $i < count($selectTopNews); $i++)
    {

           
            $post_id = $selectTopNews[$i]->post_id;
            $select_share = $wpdb->get_results("SELECT post_id, share,datetime 
                        FROM wp_analysis WHERE post_id = ".$post_id." order by datetime desc limit 1");
            if ( count($select_share) > 0)
            {
                $share = $select_share[0]->share;
            }else {$share = 0;}

            // $total_share = $selectTopNews[$i]->share;
            echo "<li><a href = ".get_permalink( $post_id )." >".get_the_title( $post_id ).
                 "</a> (share : ".$share.")</li>";
    }
}
add_shortcode('my_top_post', 'show_top_share');

function show_interesting_news()
{
    global $wpdb;
    $selectTopNews = $wpdb->get_results("SELECT post_id ,share, MAX(share)  - min(share) as focus_count 
            FROM wp_analysis WHERE timestampdiff(HOUR, Now() , dateTime) > -2
            AND  timestampdiff(HOUR, Now() , dateTime) <= 0
            group by post_id order by focus_count desc limit 10");

    echo "<h3>Interesting News in 2 hours</h3>";
    for ($i = 0; $i < count($selectTopNews); $i++ )
    {
            $share = $selectTopNews[$i]->focus_count;
            $post_id = $selectTopNews[$i]->post_id;
            $select_share = $wpdb->get_results("SELECT post_id, share,datetime 
            FROM wp_analysis WHERE post_id = ".$post_id." order by datetime desc limit 1");
            if ( count($select_share) > 0)
            {
                $total_share = $select_share[0]->share;
            }
            echo "<li><a href = ".get_permalink( $post_id )." >".get_the_title( $post_id ).
            "</a> share  (".$share." of ".$total_share.")</li>";
    }
}
add_shortcode('my_interest_News', 'show_interesting_news');

?>