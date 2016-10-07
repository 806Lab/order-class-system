/**
 * Created by kalen on 10/6/16.
 */

import React from 'react';
import EditableUser from './editable_user'

class UserList extends React.Component{
	render () {
		var userList = [];
		for (var i = 0; i < this.props.data.length; i++){
			var user = this.props.data[i];
			userList.push(<EditableUser key={user.id} data={user} />);
		}
		return <tbody>{userList}</tbody>
	}
}

export default UserList;