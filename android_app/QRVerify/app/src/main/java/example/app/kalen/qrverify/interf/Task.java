package example.app.kalen.qrverify.interf;

/**
 * Created by kalen on 2017/6/8.
 */

public interface Task {
    public String excuteBackGround( String guid) throws Exception;
    public void excute(String json);
}
