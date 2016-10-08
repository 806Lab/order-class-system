/**
 * Created by kalen on 10/6/16.
 */
import Api from '../utils/api'


class UserManagementAction {
	getUserList(data, f) {
		Api.ajaxPost('/api.php/admin/get_user_list', data, (data) => {
				if (data.msg == "你还没有登陆") {
					location.pathname = "/#";
				} else if (data.code == 0) {
					f(data.data.users, data.data.count);
				}else {
					swal({title: data.msg,   text: "", confirmButtonColor: "#ff0000"});
				}
		})
	}
	
	updateUser(data, f) {
		
	}
	
	deleteUser(data, f) {
		
	}
}

export default UserManagementAction;