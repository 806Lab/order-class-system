/**
 * Created by kalen on 10/6/16.
 */

import React from 'react';
import EditableUser from './editable_user'

class UserList extends React.Component{
	render () {
		if (this.props.data.length == 0){
			return <center><p>没有搜索到任何用户</p></center>
		}
		var userList = [];
		for (var i = 0; i < this.props.data.length; i++){
			var user = this.props.data[i];
			userList.push(<EditableUser key={user.id} data={user} />);
		}
		return <tbody>{userList}</tbody>
	}
}

export default UserList;