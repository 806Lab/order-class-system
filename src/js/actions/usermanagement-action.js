/**
 * Created by kalen on 10/6/16.
 */
import Api from '../utils/api'


class UserManagementAction {
	searchUsers(data, f) {
		Api.ajaxPost('/api.php/admin/search_users', data, (data) => {
				if (data.msg == "你还没有登陆") {
					location.pathname = "/#";
				} else if (data.code == 0) {
					f(data.data.users, data.data.count);
				}else {
					swal({title: data.msg,   text: "", confirmButtonColor: "#ff0000"});
				}
		})
	}

	addUser(data, f) {
		Api.ajaxPost('/api.php/admin/add_user', data, (data) => {
			if (data.msg == "你还没有登陆") {
				location.pathname = "/#";
			} else {
				swal({title: data.msg, text: "", confirmButtonColor: "#ff0000"});
				if (data.code == 0) {
					f();
				}
			}
		})

	}
	
	updateUser(data, f) {
		Api.ajaxPost('/api.php/admin/update_user', data, (data) => {
			if (data.msg == "你还没有登陆") {
				location.pathname = "/#";
			} else {
				swal({title: data.msg, text: "", confirmButtonColor: "#ff0000"});
				if (data.code == 0) {
					f();
				}
			}
		})
	}
	
	deleteUser(data, f) {
		Api.ajaxPost('/api.php/admin/delete_user', data, (data) => {
			if (data.msg == "你还没有登陆") {
				location.pathname = "/#";
			} else {
				swal({title: data.msg, text: "", confirmButtonColor: "#ff0000"});
				if (data.code == 0) {
					f();
				}
			}
		})
		
	}
}

export default UserManagementAction;