/**
 * Author kevinwang
 * 2015-12-6
 * manage-order action
 */

let AppConstants = require('../constants/app-constants');
let AppDispatcher = require('../dispatchers/app-dispatcher');

let Api = require('../utils/api.js');


let ManageAction = {
	/**
	 * 获取待管理预约
	 * @param  {[int]}  offset      [控制显示个数]
	 * @param  {Boolean} is_operated [预约是否处理]
	 * @return {[type]}              [description]
	 */
	getOrder: function(offset,is_operated) {
			var data = {
							'offset':offset,
							'is_operated':is_operated
					};
		Api.ajaxPost('/api.php/admin/get_order',data,function(data){
				if (data.msg == "你还没有登陆") {
						location.pathname = "/";
				} else if (data.code == 0) {
						AppDispatcher.handleViewAction({'actionType': AppConstants.GET_ADMIN_NEW_ORDER,'data':data.data.infos});
				}
		})
	},
	/**
	 * 管理预约
	 * @param  {[object]} data [处理订单id,同意或拒绝]
	 * @return {[type]}      [description]
	 */
	handleOrder: function(data) {
			Api.ajaxPost('/api.php/admin/handle_order',data,function(data){
					if (data.msg == "你还没有登陆") {
							location.pathname = "/";
					} else if (data.code == 0) {
							AppDispatcher.handleViewAction({'actionType': AppConstants.HANDLE_ORDER,'data':data.data.infos});
							ManageAction.getOrder(0, 0);
					}
			})
	},

	/**
	 * 接受多个预约
	 * @param data
	 */
	acceptMultiOrders(data) {
		Api.ajaxPost('/api.php/admin/accept_multi_orders', data, function(data){
			if (data.msg == "你还没有登陆") {
				location.pathname = "/";
			} else {
				AppDispatcher.handleViewAction({'actionType': AppConstants.HANDLE_ORDER, 'data':data.data.infos});
				ManageAction.getOrder(0, 0);
			}
		})
	},


	/**
	 * 拒绝多个预约
	 * @param data
	 */
	rejectMultiOrders(data) {
		Api.ajaxPost('/api.php/admin/reject_multi_orders', data, function(data){
			if (data.msg == "你还没有登陆") {
				location.pathname = "/";
			} else {
				AppDispatcher.handleViewAction({'actionType': AppConstants.HANDLE_ORDER, 'data':data.data.infos});
				ManageAction.getOrder(0, 0);
			}
		})
	},

	/**
	 * 清理过期预约
	 */
	cleanOutDatedOrders() {
		Api.ajaxPost('/api.php/admin/clean_outdated_orders', {}, function(data){
			if (data.msg == "你还没有登陆") {
				location.pathname = "/";
			} else {
				swal({title: data.msg, timer:2000});
				AppDispatcher.handleViewAction({'actionType': AppConstants.HANDLE_ORDER, 'data':data.data.infos});
				ManageAction.getOrder(0, 0);
			}
		})
	}
};

module.exports = ManageAction;