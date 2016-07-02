<?php
/**
 * @file
 * Default theme implementation to display a Facebook Wall posts in a single
 * drupal page.
 *
 * Available variables:
 *
 * Facebook wall (object array )
 * - $facebook_wall : Consisit aray of FB wall post of the desire user or page.
 *   It contains only the users wall post, photo, videos, link. No infomation
 *   for their freinds or followers.
 *   
 * Facebook Page (object array) 
 * - $facebook_page : Consisit of aray of FB basic page or user information.
 *   It contains the information of Two type:
 *   1) User profile => users ID, name, username, link & picture
 *   2) FB Page => PageID, name, username, cover page image, about, category,
 *   website and diaplay picture.
 * 
 * FB user Page name
 *   $fb_page_name
 * 
 * FB user Page Link
 *   $fb_page_link
 * 
 * FB user Page picture
 *   $fb_page_picture
 * 
 * FB Bace URL
 *   $fb_base_url
 *
 * @see _facebook_wall_profile_picture($user_id)
 */
$fb_base_url = 'https://www.facebook.com/';
// FB user Page name.
$fb_page_name = $facebook_page->name;
// FB user Page Link.
if (isset($facebook_page->link)) :
  $fb_page_link = $facebook_page->link;
else :
  $fb_page_link = $fb_base_url . $facebook_page->id;
endif;

// FB user Page picture.
$user_id = $facebook_page->id;
if (_facebook_wall_profile_picture($user_id) != FALSE) :
  $fb_page_picture = _facebook_wall_profile_picture($user_id);
endif;

/*
  // To Check complete array of wall post & user info.
  echo '<pre>';
  print_r($facebook_page);
  print_r($facebook_wall);
  echo '</pre>';
 */
?>
<!-- Facebook Wall block : starts here ! -->
<div class='facebook_wall_outer'>
  <?php
  // pexit($facebook_wall);
  for ($i = 0; $i < count($facebook_wall->data); $i++) :

    if (isset($facebook_wall->data[$i]->actions['0']->link)) :
      $fb_post_link = $facebook_wall->data[$i]->actions['0']->link;
    else :
      $fb_post_link = '#';
    endif;

    $flag = 0;
    // Type : Link permission.
    if (($facebook_wall->data[$i]->type == 'link') && (variable_get('facebook_wall_post_type_link') == 1)) :
      $flag = 1;
    // Type : Photo permission.
    elseif (($facebook_wall->data[$i]->type == 'photo') && (variable_get('facebook_wall_post_type_photo') == 1)) :
      $flag = 1;
    // Type : Status permission.
    elseif (($facebook_wall->data[$i]->type == 'status') && (variable_get('facebook_wall_post_type_status') == 1)) :
      $flag = 1;
    // Type : video permission.
    elseif (($facebook_wall->data[$i]->type == 'video') && (variable_get('facebook_wall_post_type_video') == 1)) :
      $flag = 1;
    endif;

    if ($flag == 1) :
      ?>

      <?php
      // Generate markup for the width of the wall
      $style_setting = variable_get('facebook_wall_width_option');
      $style_markup;
      switch ($style_setting) {
        case 'no-style':  // No width style added to the container div
          $style_markup = '';
          break;

        case 'relative-width':  // Relative width style added to the container div
          $post_width_size = variable_get('facebook_wall_width_size_relative');
          $style_markup = ' style="width:' . $post_width_size . '%"';
          break;

        case 'pixel-width':  // Pixel width style added to the container div
          $post_width_size = variable_get('facebook_wall_width_size');
          $style_markup = ' style="width:' . $post_width_size . 'px"';
          break;

        default:  // No width style added to the container div
          $style_markup = ' style="width:' . $post_width_size . '%"';
      }
      ?>
      <div class='facebook_wall' <?php print 'id="' . $facebook_wall->data[$i]->id . '"' . $style_markup; ?> >
        <!-- Left Image Icon : starts here ! -->
        <div class='wallContent_left'>
		<div class="facebook_thumb">
          <?php
          if (isset($fb_page_picture)) :
            print '<img src="' . $fb_page_picture . '" />';
          endif;
          ?>
		</div>
		<div class="facebook_thumb_info">
		 <!-- Headline Block : Starts here ! -->
          <strong>
            <?php
            if (isset($fb_page_name)) :
              print '<a href="' . $fb_page_link . '">' . $fb_page_name . '</a>';
            endif;
            ?>
          </strong>
          <span class="headline">
            <?php
            if (isset($facebook_wall->data[$i]->link) && $facebook_wall->data[$i]->type == 'link') :
              if (isset($facebook_wall->data[$i]->story)) :
                print '<a href="' . $facebook_wall->data[$i]->link . '">' . str_replace($fb_page_name, '', $facebook_wall->data[$i]->story) . '</a>';
              else :
                print 'shared a <a href="' . $facebook_wall->data[$i]->link . '">Link</a>';
              endif;
            elseif (isset($facebook_wall->data[$i]->story) && $facebook_wall->data[$i]->type == 'photo') :
              print str_replace($fb_page_name, '', $facebook_wall->data[$i]->story);
            elseif (isset($facebook_wall->data[$i]->link) && $facebook_wall->data[$i]->type == 'video') :
              print 'shared a <a href="' . $facebook_wall->data[$i]->link . '">Video</a>';
            endif;
            ?>
			<div class="caption">
              <?php
              if (isset($facebook_wall->data[$i]->created_time)) :
                print date('F d, Y', strtotime($facebook_wall->data[$i]->created_time));
                print ' at ' . date('h:ia', strtotime($facebook_wall->data[$i]->created_time));
              endif;
              ?>
            </div>
          </span>
          <!-- Headline block : ends here ! -->
		</div>
        </div>
        <!-- Left Image Icon : ends here ! -->

        <!-- Wall right content Block : starts here ! -->
        <div class="wallContent_right">
          <!-- User Content block : starts here ! -->
          <div class="rtejustify userContent"> 
            <?php
            // FB story message.
            if (isset($facebook_wall->data[$i]->message)) :
              print $facebook_wall->data[$i]->message;
            elseif (isset($facebook_wall->data[$i]->story) && $facebook_wall->data[$i]->type == 'status') :
              print $facebook_wall->data[$i]->story;
            endif;
            ?>

          </div>
          <!-- User Content block : ends here ! -->

          <!-- Share link block : starts here ! -->
          <?php
          if (($facebook_wall->data[$i]->type == 'link') && isset($facebook_wall->data[$i]->name)) :
            if (isset($facebook_wall->data[$i]->link)) :
              print '<a href="' . $facebook_wall->data[$i]->link . '" >';
            endif;
            ?>
            <div class="shareLink">
              <div class="shareLink_left">
                <?php
                // Share Picture.
                if (isset($facebook_wall->data[$i]->picture)) :
                  print '<img src="' . $facebook_wall->data[$i]->picture . '">';
                endif;
                ?>
              </div>
              <div class="shareLink_right">
                <span>
                  <?php
                  if (isset($facebook_wall->data[$i]->name) && isset($facebook_wall->data[$i]->link)) :
                    print '<strong><a href="' . $facebook_wall->data[$i]->link . '">' . $facebook_wall->data[$i]->name . '</a></strong>';
                  elseif (isset($facebook_wall->data[$i]->name)) :
                    print '<strong><a href="#">' . $facebook_wall->data[$i]->name . '</a></strong>';
                  endif;
                  ?>
                </span>
                <div class="caption">
                  <?php
                  if (isset($facebook_wall->data[$i]->caption)) :
                    print $facebook_wall->data[$i]->caption;
                  endif;
                  ?>
                </div>
                <div class="caption">
                  <?php
                  if (isset($facebook_wall->data[$i]->description)) :
                    print $facebook_wall->data[$i]->description;
                  endif;
                  ?>
                </div>
              </div>
            </div>
            <?php
            if (isset($facebook_wall->data[$i]->link)) :
              print '</a>';
            endif;
          endif;
          ?>
          <!-- share link block : ends here ! -->

          <!-- User Content block : starts here ! -->
          <div> 
            <?php
            if (isset($facebook_wall->data[$i]->source) && $facebook_wall->data[$i]->type == 'video') :
             $video_path = _facebook_video_settings($facebook_wall->data[$i]->source);
            if($video_path){  
            print '<div class="embedded-video"><div class="player"><iframe allowfullscreen frameborder="0" scrolling="no" width="100%" height="100%" src="' . $video_path . '" frameborder="0"></iframe></div></div>';
            } else {
            print '<video  id="video1" width="100%" height="auto" controls preload="none" poster="'. $facebook_wall->data[$i]->picture .'" >"<source src= "'. $facebook_wall->data[$i]->source .'" type="video/mp4"></source></video>';
            }
            elseif (isset($facebook_wall->data[$i]->picture) && $facebook_wall->data[$i]->type == 'photo') :
              if (stristr($facebook_wall->data[$i]->picture, '_s.')) :
                $large_img = str_replace('_s.', '_n.', $facebook_wall->data[$i]->picture);
                print '<a href="' . $facebook_wall->data[$i]->link . '"><img src="' . $large_img . '" style="max-width:400px; max-height:400px;"></a>';
                else :
                $large_img = $facebook_wall->data[$i]->picture;
                print '<a href="' . $facebook_wall->data[$i]->link . '"><img src="' . $large_img . '" style="max-width:400px; max-height:400px;"></a>';
                endif;
                endif;
            ?>  
          </div>
          <!-- User Content block : ends here ! -->

          <!-- Likes block : starts here ! -->
          <?php
          if (isset($facebook_wall->data[$i]->likes->data)) :
            $count_likes = count($facebook_wall->data[$i]->likes->data);
            if ($count_likes > 0) :
              ?>
              <div class="likes">
                <?php
                for ($key = 0; $key < $count_likes && $key < 3; $key++) :
                  print '<a href="' . $fb_base_url . $facebook_wall->data[$i]->likes->data[$key]->id . '" >' . $facebook_wall->data[$i]->likes->data[$key]->name . '</a>';
                  if ($key < $count_likes - 1) :
                    print ', ';
                  endif;
                endfor;
                if ($key < count($facebook_wall->data[$i]->likes->data)) :
                  print 'and <a href=' . $fb_post_link . '>' . (count($facebook_wall->data[$i]->likes->data) - $key) . ' others</a>';
                  print ' likes this.';
                endif;
                ?>
              </div>
              <?php
            endif;
          endif;
          // End outer if.
          ?>
          <!-- Likes block : ends here ! -->

          <!-- Comment Block : Starts here ! -->
          
          <!-- Comment Block : Ends here ! -->

        </div>  
        <!-- Wall right content Block : starts here ! -->
			    <?php
        if (variable_get('facebook_wall_post_view') == 1) :
          ?>
          <!-- View post link : starts here ! -->
          <div class="post_link">
            <?php print '<a target="_blank" href="' . $fb_post_link . ' ">View Post</a>'; ?>
          </div>
          <?php
        else :
          echo '<div class="post_link" style="visibility:hidden;"></div>';
        endif;
        ?>

      </div>
        <!-- View post link : ends here ! -->
      <?php
    endif;
  endfor;
  ?>
</div>
<!-- Facebook Wall block : ends here ! -->

