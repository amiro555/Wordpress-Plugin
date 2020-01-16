<?php
    /*
    Plugin Name: AutoPostNews
    Description: Plugin for automatic news post from RSS Feed. 
    Version: 1.0
    Author: Amiro Phiwsawang
    License: GPLV2 or later
    */

    require( 'C:/xampp/htdocs/wordpress/wp-load.php' ); 
    
    function Add_adminMenu() {
        add_menu_page(
            __('Post news', 'textdomain' ),
            'Post news',
            'manage_options',
            'Post news',
            'get_url',
            '',
            2
        );
    }

    add_action('admin_menu', 'Add_adminMenu');

    function get_url()
    {
        ?>
        <style>
            .w3-lobster {
                font-family: "Lobster", serif;

            }

            input[type=text], select {
                width: 70%;
                padding: 12px 20px;
                margin: 8px 0;
                display: inline-block;
                border: 1px solid #ccc;
                border-radius: 4px;
                box-sizing: border-box;
                background-color: #243676;
                color: white;
            }

            input[type=submit] {
                width: 20%;
                background-color: #4CAF50;
                color: white;
                padding: 14px 20px;
                margin: 8px 0;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                }

            input[type=submit]:hover {
                background-color: #45a049;
                }

            .container {
                border-radius: 7px;
                background-color: #B3E6F7;
                padding: 50px;
                width: 80%;
                margin-left: 110px;
                margin-top:6%;
                margin-bottom:10%;
                height:80%;
                
                }  

        </style>

        <html>
            <head>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
            <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lobster">
            </head>
            <body>
                    <div class = "container">
                        <div class="w3-container w3-lobster">
                            <p class="w3-xxxlarge">Automatic News post!</p>
                        </div>    
                    
                        <form method = "post">
                            <input type="text" name = "input_url" placeholder="Enter the url..." required="TRUE" size = "50" />
                            <input type ="submit" name = "btn_url" value = "Publish" />
                        </form>    
                    </div>

                    <?php 
                        if(isset($_POST['btn_url']))
                        {
                            $url = $_POST['input_url'];
                            post_news($url);
                        }
                    ?>
            </body>
            </html>
        <?php
    }

    function post_news($url) {  

        $feed = fetch_feed($url);
        $rss_items = $feed->get_items(0);
        
        foreach ( $rss_items as $item => $number ) {
            // $single_link = $number->get_permalink();
            $get_content = $number->get_description();
            $get_title = $number->get_title();

            $my_post = array();
            $my_post['post_title']    = $get_title;
            $my_post['post_content']  = $get_content;
            $my_post['post_status']   = 'publish';
            $my_post['post_author']   = 1;
            $my_post['post_category'] = array(0);
    
            wp_insert_post( $my_post );
        
        }
        if (wp_insert_post( $my_post ))
        {
            ?> <script>alert("Successfuly");</script> <?php
        }else{ 
            ?> <script>alert("Faild");</script> <?php 
        }
    }
?>