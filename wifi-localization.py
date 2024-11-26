import numpy as np
import random
import time
import mysql.connector
from sklearn.preprocessing import StandardScaler
from sklearn.svm import SVC
from joblib import dump, load

# 1. 数据库连接
def connect_to_database():
    try:
        connection = mysql.connector.connect(
            host="wayne.cs.uwec.edu",  # 远程服务器主机名
            user="XUZ6923",           # 用户名
            password="5D74NAT4",      # 密码
            database="cs485group4",   # 数据库名
            port=3306,                # 默认 MySQL 端口
            connection_timeout=10     # 设置连接超时时间
        )
        if connection.is_connected():
            print("Successfully connected to remote database: cs485group4")
        return connection
    except mysql.connector.Error as e:
        print(f"Error connecting to database: {e}")
        return None



# 2. 随机数据插入 wifi_signals 表
def insert_random_data_to_wifi_signals():
    """每隔 5 秒插入随机 Wi-Fi 信号数据到 wifi_signals 表"""
    while True:
        connection = connect_to_database()
        if not connection:
            print("Failed to connect to database for data insertion.")
            time.sleep(5)
            continue

        try:
            random_data = [random.randint(-90, -30) for _ in range(7)]  # 模拟 Wi-Fi 信号强度
            cursor = connection.cursor()
            query = """
            INSERT INTO wifi_signals (signal_1, signal_2, signal_3, signal_4, signal_5, signal_6, signal_7)
            VALUES (%s, %s, %s, %s, %s, %s, %s)
            """
            cursor.execute(query, tuple(random_data))
            connection.commit()
            print(f"Inserted into wifi_signals: {random_data}")
        except mysql.connector.Error as e:
            print(f"Error inserting random data: {e}")
        finally:
            connection.close()  # 确保关闭连接
        time.sleep(5)

# 3. 从 random_wifi_signals 表获取最新数据
def fetch_latest_random_wifi_signals():
    """从 random_wifi_signals 表获取最新数据"""
    connection = connect_to_database()
    if not connection:
        print("Failed to connect to database for fetching data.")
        return None

    try:
        cursor = connection.cursor()
        query = """
        SELECT signal_1, signal_2, signal_3, signal_4, signal_5, signal_6, signal_7
        FROM random_wifi_signals ORDER BY id DESC LIMIT 1
        """
        cursor.execute(query)
        result = cursor.fetchone()
        if result:
            print(f"Fetched from random_wifi_signals: {result}")
            return np.array(result).reshape(1, -1)
        else:
            print("No data in random_wifi_signals.")
            return None
    except mysql.connector.Error as e:
        print(f"Error fetching data: {e}")
        return None
    finally:
        connection.close()  # 确保关闭连接

# 4. 预测所在房间并插入 user_location 表
def predict_and_insert_user_location(svm_wifi_model, scaler):
    """基于 random_wifi_signals 数据预测房间并插入到 user_location"""
    while True:
        connection = connect_to_database()
        if not connection:
            print("Failed to connect to database for prediction.")
            time.sleep(5)
            continue

        try:
            wifi_signals = fetch_latest_random_wifi_signals()
            if wifi_signals is None:
                time.sleep(5)
                continue

            # 标准化数据并预测房间
            wifi_signals_scaled = scaler.transform(wifi_signals)
            predicted_room = int(svm_wifi_model.predict(wifi_signals_scaled)[0])
            print(f"Predicted room: {predicted_room}")

            # 插入预测结果到 user_location
            cursor = connection.cursor()
            query = "INSERT INTO user_location (room, timestamp) VALUES (%s, NOW())"
            cursor.execute(query, (predicted_room,))
            connection.commit()
            print(f"Inserted into user_location: room={predicted_room}")
        except mysql.connector.Error as e:
            print(f"Error inserting user location: {e}")
        except Exception as e:
            print(f"Prediction error: {e}")
        finally:
            connection.close()  # 确保关闭连接
        time.sleep(5)

# 5. 加载或训练模型
def load_or_train_model():
    """加载模型或训练新模型"""
    try:
        svm_wifi_model = load('svm_wifi_localization_model.joblib')
        scaler = load('scaler.joblib')
        print("Models loaded successfully.")
    except FileNotFoundError:
        print("Model files not found. Please train a new model.")
        exit()  # 如果模型不存在，则直接退出程序
    return svm_wifi_model, scaler

# 主程序
if __name__ == "__main__":
    # 加载模型
    svm_wifi_model, scaler = load_or_train_model()

    # 启动线程进行数据插入和预测
    try:
        from threading import Thread
        thread_insert = Thread(target=insert_random_data_to_wifi_signals)
        thread_predict = Thread(target=predict_and_insert_user_location, args=(svm_wifi_model, scaler))

        thread_insert.start()
        thread_predict.start()

        thread_insert.join()
        thread_predict.join()
    except KeyboardInterrupt:
        print("Program terminated by user.")
