<?php
/**
 * 评论管理
 * @copyright (c) Emlog All Rights Reserved
 */

require_once 'globals.php';

$Comment_Model = new Comment_Model();

if ($action == '') {
    $blogId = isset($_GET['gid']) ? intval($_GET['gid']) : null;
    $hide = isset($_GET['hide']) ? addslashes($_GET['hide']) : '';
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;

    $addUrl_1 = $blogId ? "gid={$blogId}&" : '';
    $addUrl_2 = $hide ? "hide=$hide&" : '';
    $addUrl = $addUrl_1 . $addUrl_2;

    $comment = $Comment_Model->getComments(1, $blogId, $hide, $page);
    $cmnum = $Comment_Model->getCommentNum($blogId, $hide);
    $hideCommNum = $Comment_Model->getCommentNum($blogId, 'y');
    $pageurl = pagination($cmnum, Option::get('admin_perpage_num'), $page, "comment.php?{$addUrl}page=");

    include View::getView('header');
    require_once(View::getView('comment'));
    include View::getView('footer');
    View::output();
}

if ($action == 'del') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : '';

    LoginAuth::checkToken();

    $Comment_Model->delComment($id);
    $CACHE->updateCache(array('sta', 'comment'));
    emDirect("./comment.php?active_del=1");
}

if ($action == 'delbyip') {
    LoginAuth::checkToken();
    if (ROLE != ROLE_ADMIN) {
        emMsg('权限不足！', './');
    }
    $ip = isset($_GET['ip']) ? $_GET['ip'] : '';
    $Comment_Model->delCommentByIp($ip);
    $CACHE->updateCache(array('sta', 'comment'));
    emDirect("./comment.php?active_del=1");
}

if ($action == 'hide') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : '';
    $Comment_Model->hideComment($id);
    $CACHE->updateCache(array('sta', 'comment'));
    emDirect("./comment.php?active_hide=1");
}
if ($action == 'show') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : '';
    $Comment_Model->showComment($id);
    $CACHE->updateCache(array('sta', 'comment'));
    emDirect("./comment.php?active_show=1");
}

if ($action == 'admin_all_coms') {
    $operate = isset($_POST['operate']) ? $_POST['operate'] : '';
    $comments = isset($_POST['com']) ? array_map('intval', $_POST['com']) : array();

    if ($operate == '') {
        emDirect("./comment.php?error_b=1");
    }
    if ($comments == '') {
        emDirect("./comment.php?error_a=1");
    }
    if ($operate == 'del') {
        $Comment_Model->batchComment('delcom', $comments);
        $CACHE->updateCache(array('sta', 'comment'));
        emDirect("./comment.php?active_del=1");
    }
    if ($operate == 'hide') {
        $Comment_Model->batchComment('hidecom', $comments);
        $CACHE->updateCache(array('sta', 'comment'));
        emDirect("./comment.php?active_hide=1");
    }
    if ($operate == 'pub') {
        $Comment_Model->batchComment('showcom', $comments);
        $CACHE->updateCache(array('sta', 'comment'));
        emDirect("./comment.php?active_show=1");
    }
}

if ($action == 'reply_comment') {
    include View::getView('header');
    $commentId = isset($_GET['cid']) ? intval($_GET['cid']) : '';
    $commentArray = $Comment_Model->getOneComment($commentId);
    extract($commentArray);

    require_once(View::getView('comment_reply'));
    include View::getView('footer');
    View::output();
}

if ($action == 'edit_comment') {
    $commentId = isset($_GET['cid']) ? intval($_GET['cid']) : '';
    $commentArray = $Comment_Model->getOneComment($commentId, FALSE);
    if (!$commentArray) {
        emMsg('不存在该评论！', './comment.php');
    }
    extract($commentArray);

    include View::getView('header');
    require_once(View::getView('comment_edit'));
    include View::getView('footer');
    View::output();
}

if ($action == 'doreply') {
    $reply = isset($_POST['reply']) ? trim(addslashes($_POST['reply'])) : '';
    $commentId = isset($_POST['cid']) ? intval($_POST['cid']) : '';
    $blogId = isset($_POST['gid']) ? intval($_POST['gid']) : '';
    $hide = isset($_POST['hide']) ? addslashes($_POST['hide']) : 'n';

    if ($reply == '') {
        emDirect("./comment.php?error_c=1");
    }
    if (strlen($reply) > 2000) {
        emDirect("./comment.php?error_d=1");
    }
    //回复一条待审核的评论，视为要将其公开（包括回复内容）
    if ($hide == 'y') {
        $Comment_Model->showComment($commentId);
        $hide = 'n';
    }
    $Comment_Model->replyComment($blogId, $commentId, $reply, $hide);
    $CACHE->updateCache('comment');
    $CACHE->updateCache('sta');
    doAction('comment_reply', $commentId, $reply);
    emDirect("./comment.php?active_rep=1");
}
