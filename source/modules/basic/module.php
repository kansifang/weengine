<?php
/**
 * 基本文字回复模块
 * 
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');

class BasicModule extends WeModule {
	public $name = 'BasicChatRobotModule';
	public $title = '自定义回复';
	public $ability = '';
    public $tablename = 'basic_reply';
	
	public function fieldsFormDisplay($rid = 0) {
		if (!empty($rid) && $rid > 0) {
			$isexists = pdo_fetch("SELECT id FROM ".tablename('rule')." WHERE id = :id", array(':id' => $rid));
		}
		
		if (!empty($isexists)) {
			$reply = pdo_fetchall("SELECT * FROM ".tablename($this->tablename)." WHERE rid = :rid", array(':rid' => $rid));	
		}
		include template('modules/basic/display', TEMPLATE_INCLUDEPATH);
	}
	
	public function fieldsFormValidate($rid = 0) {
		return true;	
	}
	
	public function fieldsFormSubmit($rid = 0) {
		global $_GPC;
		if (!empty($_GPC['reply'])) {
			foreach ($_GPC['reply'] as $id => $row) {	
				pdo_update($this->tablename, array('content' => $row), array('id' => $id));
			}
		}
		if (!empty($_GPC['reply-new'])) {
			$replies = array();
			foreach ($_GPC['reply-new'] as $id => $row) {
				$replies[] = "('$rid', '{$row}')";
			}
			$sql = "INSERT INTO ".tablename($this->tablename)."(rid, content) VALUES ".implode(',', $replies);
			pdo_query($sql);
		}	
		
		if (!empty($_GPC['delete'])) {
			pdo_delete($this->tablename, "id IN ('".implode("','", $_GPC['delete'])."')");
		}
		return true;	
	}
	
	public function ruleDeleted($rid = 0) {
		    
	}
	
	public function doDelete() {
		global $_W,$_GPC;
		$id = intval($_GPC['id']);
		$sql = "SELECT id, rid FROM " . tablename($this->tablename) . " WHERE `id`=:id";
		$row = pdo_fetch($sql, array(':id'=>$id));
		if (empty($row)) {
			message('抱歉，回复不存在或是已经被删除！', '', 'error');
		}
		 
		if (pdo_delete($this->tablename, array('id' => $id))) {
			message('删除回复成功', '', 'success');
		} 
	}
	
}
