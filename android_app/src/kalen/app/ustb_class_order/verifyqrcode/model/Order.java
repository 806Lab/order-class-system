package kalen.app.ustb_class_order.verifyqrcode.model;

import java.io.Serializable;

/**
 * 借教室的请求bean
 * @author kalen
 *
 */
public class Order implements Serializable{
	/**
	 * 
	 */
	private static final long serialVersionUID = 1L;
	private String date;	//哪天
	private String room;	//哪个教室
	private String time;	//借的教室时间段
	private String username; //谁借的教室
	private String unitInfo; //借教室的人所属单位
	
	public Order(String date, String room, String time, String username,
			String unitInfo) {
		super();
		this.date = date;
		this.room = room;
		this.time = time;
		this.username = username;
		this.unitInfo = unitInfo;
	}
	
	public String getDate() {
		return date;
	}
	public void setDate(String date) {
		this.date = date;
	}
	public String getRoom() {
		return room;
	}
	public void setRoom(String room) {
		this.room = room;
	}
	public String getTime() {
		return time;
	}
	public void setTime(String time) {
		this.time = time;
	}
	public String getUsername() {
		return username;
	}
	public void setUsername(String username) {
		this.username = username;
	}
	public String getUnitInfo() {
		return unitInfo;
	}
	public void setUnitInfo(String unitInfo) {
		this.unitInfo = unitInfo;
	}
	
	

}
