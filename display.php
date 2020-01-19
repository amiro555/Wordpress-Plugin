<?php


function show_top_share()
{
    
    global $wpdb;
    $selectTopNews = $wpdb->get_results("SELECT post_id , share , sum(share) as totalshared
                        FROM post_analysis Group by post_id
                        Order by totalshared desc limit 10");


    echo "<h3>TOP NEWS</h3>";

    $num = count($selectTopNews);
    for ($i = 0; $i < $num; $i++)
    {
       
        $post_id = $selectTopNews[$i]->post_id;
        $select_share = $wpdb->get_results("SELECT post_id, share,datetime 
                    FROM post_analysis WHERE post_id = ".$post_id." order by datetime desc limit 1");
        
        $num_share = count($select_share);
        if ( $num_share > 0)
        {
            $share = $select_share[0]->share;
        }

        // $total_share = $selectTopNews[$i]->share;
        echo "<li><a href = ".get_permalink( $post_id )." >".get_the_title( $post_id ).
                "</a> (share : ".$share.")</li>";
    }
    unset($selectTopNews);
    unset($select_share);
}
add_shortcode('my_top_post', 'show_top_share');

function show_interesting_news()
{
    global $wpdb;
    $selectTopNews = $wpdb->get_results("SELECT post_id ,share, MAX(share)  - min(share) as focus_count 
            FROM post_analysis WHERE timestampdiff(HOUR, Now() , dateTime) > -2
            AND  timestampdiff(HOUR, Now() , dateTime) <= 0
            group by post_id order by focus_count desc limit 10");

    echo "<h3>Interesting News in 2 hours</h3>";

    $num = count($selectTopNews);
    for ($i = 0; $i < $num; $i++ )
    {
        $share = $selectTopNews[$i]->focus_count;
        $post_id = $selectTopNews[$i]->post_id;
        $select_share = $wpdb->get_results("SELECT post_id, share,datetime 
        FROM post_analysis WHERE post_id = ".$post_id." order by datetime desc limit 1");

        $num_share = count($select_share);
        if ($num_share  > 0)
        {
            $total_share = $select_share[0]->share;
        }
        echo "<li><a href = ".get_permalink( $post_id )." >".get_the_title( $post_id ).
        "</a> share  (".$share." of ".$total_share.")</li>";
    }
    unset($selectTopNews);
    unset($select_share);
}
add_shortcode('my_interest_News', 'show_interesting_news');

?>