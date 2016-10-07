/**
 * Created by kalen on 10/5/16.
 */
import React from 'react';
import AuthenticationMixin from '../../mixins/AuthenticationMixin';
import UserManagementAction from '../../actions/usermanagement-action';
import UserList from './usermanagement/userlist'

const action = new UserManagementAction();

const UserManagement = React.createClass({
    mixins: [ AuthenticationMixin ],
    getInitialState: function () {
        return {
            users: [],
            offset: 0
        }
    },
    componentDidMount ()  {
        this.getUserList();
    },
    getUserList() {
        var that = this;
        action.getUserList({offset: this.state.offset}, (data) => {
            that.setState({users:data});
        })
    },
    render: function () {
        return (
            <table className="table table-striped table-hover text-center">
                <thead>
                    <tr>
                        <th>学号(工号)</th>
                        <th>姓名</th>
                        <th>单位</th>
                        <th>手机号</th>
                        <th>用户类型</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <UserList data={this.state.users} />
            </table>
        )
    }
});

module.exports = UserManagement;