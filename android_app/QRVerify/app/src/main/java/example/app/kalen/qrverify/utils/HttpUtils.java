package example.app.kalen.qrverify.utils;

import org.apache.http.HttpResponse;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.protocol.BasicHttpContext;
import org.apache.http.protocol.HttpContext;
import org.apache.http.util.EntityUtils;

/**
 * Created by kalen on 2017/6/8.
 */

public class HttpUtils {
    private static final String GET_INFO_URL_STRING = "http://ccs.nladuo.tech/api.php/verify/get_qrcode_info";
    private static final String COMMIT_REQUEST_URL_STRING = "http://ccs.nladuo.tech/api.php/verify/nxnjznjxnsaxiahiueiqiojw";

    public static String getInfo(String guid) throws Exception{
        HttpContext context = new BasicHttpContext();
        String url = GET_INFO_URL_STRING + "?guid=" + guid;

        HttpGet httpget=new HttpGet(url);
        HttpResponse response=new DefaultHttpClient().execute(httpget,context);
        System.out.println(response.getStatusLine().getStatusCode());
        if(response.getStatusLine().getStatusCode() == 200){
            //如果状态码为200,就是得到Json
            return  EntityUtils.toString(response.getEntity());
        }else{
            throw new Exception();
        }
    }

    public static String commitRequest(String guid) throws Exception{

        HttpContext context = new BasicHttpContext();
        String url = COMMIT_REQUEST_URL_STRING + "?guid=" + guid;

        HttpGet httpget=new HttpGet(url);
        HttpResponse response=new DefaultHttpClient().execute(httpget,context);
        if(response.getStatusLine().getStatusCode() == 200){
            //如果状态码为200,就是得到Json
            return  EntityUtils.toString(response.getEntity());
        }else{
            throw new Exception();
        }
    }
}
