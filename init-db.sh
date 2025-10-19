#!/bin/bash

# รอให้ MySQL พร้อมก่อน
echo "⏳ รอ MySQL เริ่มต้น..."
sleep 5

# ตรวจสอบว่ามี database หรือยัง ถ้าไม่มีก็นำเข้า
echo "📥 กำลังนำเข้า uniconnect_db.sql..."
/opt/lampp/bin/mysql -u root < /opt/lampp/htdocs/UniConnect/uniconnect_db.sql

echo "✅ นำเข้าเสร็จแล้ว!"
# รัน XAMPP ตามปกติ
/opt/lampp/lampp start
tail -f /opt/lampp/logs/error_log
