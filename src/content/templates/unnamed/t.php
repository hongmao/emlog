<?php 
/*
* 碎语部分
*/
if(!defined('EMLOG_ROOT')) {exit('error!');} 
?>
<div id="content">
    <?php if(ROLE == 'admin' || ROLE == 'writer'): ?>
    <div class="post"><a href="<?php echo BLOG_URL . 'admin/twitter.php' ?>">发布碎语</a></div>
    <?php endif; ?>
    <?php 
    foreach($tws as $val):
    $author = $user_cache[$val['author']]['name'];
    $avatar = empty($user_cache[$val['author']]['avatar']) ? 
                BLOG_URL . 'admin/views/images/avatar.jpg' : 
                BLOG_URL . $user_cache[$val['author']]['avatar'];
    $tid = (int)$val['id'];
    ?> 
    <div class="post tw">
    	<div class="avatar"><img src="<?php echo $avatar; ?>" width="32px" height="32px" /></div>
    	<div class="tw-info">
    		<div class="tw-reply"><a href="javascript:loadr('<?php echo DYNAMIC_BLOGURL; ?>?action=getr&tid=<?php echo $tid;?>','<?php echo $tid;?>');">回复(<span id="rn_<?php echo $tid;?>"><?php echo $val['replynum'];?></span>)</a></div>
    		<div class="tw-meta"><?php echo $author; ?> <span class="tw-time"><?php echo $val['date']; ?></span></div>
    		<div class="tw-content"><?php echo $val['t'];?></div>
    	</div>
   		<ul id="r_<?php echo $tid;?>" class="r"></ul>
    	<div class="tw-form" id="rp_<?php echo $tid;?>">   
			<textarea id="rtext_<?php echo $tid; ?>"></textarea>
    		<div class="tw-input">
        		<span class="tw-nnc" style="<?php if(ISLOGIN){echo 'display:none';}?>">
        		昵称：<input type="text" id="rname_<?php echo $tid; ?>" value="" />
        		<span style="display:<?php if($reply_code == 'n'){echo 'none';}?>">验证码：<input type="text" id="rcode_<?php echo $tid; ?>" value="" /><?php echo $rcode; ?></span>        
        		</span>
        		<input class="tw-button" type="button" onclick="reply('<?php echo DYNAMIC_BLOGURL; ?>index.php?action=reply',<?php echo $tid;?>);" value="回复" /> 
        		<div class="tw-msg"><span id="rmsg_<?php echo $tid; ?>" style="color:#FF0000"></span></div>
    		</div>
    	</div>
    </div>
    <?php endforeach;?>
	<div id="pagenavi"><?php echo $pageurl;?></div>
</div><!--end content-->
<?php include View::getView('side'); include View::getView('footer'); ?>