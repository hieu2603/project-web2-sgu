const API_URL = 'https://provinces.open-api.vn/api';

const selectProvince = document.getElementById('selectProvince');
const selectDistrict = document.getElementById('selectDistrict');
const selectWard = document.getElementById('selectWard');

// Lấy danh sách tỉnh/thành phố
fetch(`${API_URL}/p`)
  .then(response => response.json())
  .then(data => {
    const provinces = data;

    provinces.forEach(province => {
      selectProvince.innerHTML += `
        <option value="${province.name}" data-code="${province.code}">
          ${province.name}
        </option>
      `;
    });
  })
  .catch(error => console.error('Lỗi khi gọi API: ', error));

// Lấy danh sách quận/huyện
function fetchDistricts(provinceCode) {
  fetch(`${API_URL}/p/${provinceCode}/?depth=2`)
    .then(response => response.json())
    .then(data => {
      const districts = data.districts;

      selectDistrict.innerHTML = `<option value="">-- Chọn quận/huyện --</option>`;

      districts.forEach(district => {
        selectDistrict.innerHTML += `
          <option value="${district.name}" data-code="${district.code}">
            ${district.name}
          </option>
        `;
      });
    })
    .catch(error => console.error('Lỗi khi gọi API: ', error));
}

// Lấy danh sách phường/xã
function fetchWards(districtCode) {
  fetch(`${API_URL}/d/${districtCode}/?depth=2`)
    .then(response => response.json())
    .then(data => {
      const wards = data.wards;

      selectWard.innerHTML = `<option value="">-- Chọn phường/xã --</option>`;

      wards.forEach(ward => {
        selectWard.innerHTML += `
          <option value="${ward.name}" data-code="${ward.code}">
            ${ward.name}
          </option>
        `;
      });
    })
    .catch(error => console.error('Lỗi khi gọi API: ', error));
}

selectProvince.addEventListener('change', event => {
  const provinceCode = selectProvince.selectedOptions[0].dataset.code;

  // Reset select quận/huyện và phường/xã
  selectDistrict.innerHTML = `<option value="">-- Chọn quận/huyện --</option>`;
  selectWard.innerHTML = `<option value="">-- Chọn phường/xã --</option>`;

  if (!provinceCode) return;

  fetchDistricts(provinceCode);
})

selectDistrict.addEventListener('change', event => {
  const districtCode = selectDistrict.selectedOptions[0].dataset.code;

  if (!districtCode) {
    selectWard.innerHTML = `<option value="">-- Chọn phường/xã --</option>`;
    return;
  }

  fetchWards(districtCode);
})