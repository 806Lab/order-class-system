package kalen.app.ustb_class_order.verifyqrcode.utils;

import org.json.JSONException;
import org.json.JSONObject;
import org.json.JSONTokener;

import kalen.app.ustb_class_order.verifyqrcode.model.Order;

public class JsonParser {
	
	public static String getMsg(String json) throws JSONException{
		JSONTokener jsonTokener = new JSONTokener(json);
        JSONObject jsonObject = (JSONObject) jsonTokener.nextValue();
        return jsonObject.getString("msg");
	}
	
	public static int getCode(String json) throws JSONException{
		JSONTokener jsonTokener = new JSONTokener(json);
        JSONObject jsonObject = (JSONObject) jsonTokener.nextValue();
        return jsonObject.getInt("code");
	}
	
	public static Order getOrder(String json) throws JSONException{
		JSONTokener jsonTokener = new JSONTokener(json);
        JSONObject jsonObject = (JSONObject) jsonTokener.nextValue();
        JSONObject data =  jsonObject.getJSONObject("data");
        String date = data.getString("date");
        String room = data.getJSONObject("room").getString("room_num");
        String time = data.getJSONObject("time").getString("time_info");
        String username = data.getJSONObject("user").getString("name");
        String unitInfo = data.getJSONObject("user").getString("unit_info");
        
		return new Order(date, room, time, username, unitInfo);
        
	}
	

}
