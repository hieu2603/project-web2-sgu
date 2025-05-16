<?php

if ($order_row['order_status'] == 'Hủy đơn') {
  echo 'text-danger';
} elseif ($order_row['order_status'] == 'Đã xác nhận') {
  echo 'text-warning';
} elseif ($order_row['order_status'] == 'Thành công') {
  echo 'text-success';
} else {
  echo 'text-secondary';
}
