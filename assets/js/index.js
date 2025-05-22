const API_URL = 'https://provinces.open-api.vn/api';

const selectProvince = document.getElementById('selectProvince');
const selectDistrict = document.getElementById('selectDistrict');
const selectWard = document.getElementById('selectWard');

const selectedProvince = document.getElementById('selectedProvince')?.value || '';
const selectedDistrict = document.getElementById('selectedDistrict')?.value || '';
const selectedWard = document.getElementById('selectedWard')?.value || '';

// Lấy danh sách tỉnh/thành phố
fetch(`${API_URL}/p`)
  .then(response => response.json())
  .then(provinces => {
    selectProvince.innerHTML = `<option value="">Chọn tỉnh/thành phố</option>`;

    provinces.forEach(province => {
      const option = document.createElement('option');
      option.value = province.name;
      option.dataset.code = province.code;
      option.textContent = province.name;

      if (province.name === selectedProvince) {
        option.selected = true;
        fetchDistricts(province.code, selectedDistrict, selectedWard);
      }

      selectProvince.appendChild(option);
    });

    // Nếu không chọn địa chỉ có sẵn thì không cần tự động chọn gì thêm
  })
  .catch(error => console.error('Lỗi khi gọi API: ', error));

function fetchDistricts(provinceCode, selectedDistrict = '', selectedWard = '') {
  fetch(`${API_URL}/p/${provinceCode}/?depth=2`)
    .then(response => response.json())
    .then(data => {
      const districts = data.districts;

      selectDistrict.innerHTML = `<option value="">Chọn quận/huyện</option>`;
      selectWard.innerHTML = `<option value="">Chọn phường/xã</option>`;

      districts.forEach(district => {
        const option = document.createElement('option');
        option.value = district.name;
        option.dataset.code = district.code;
        option.textContent = district.name;

        if (district.name === selectedDistrict) {
          option.selected = true;
          fetchWards(district.code, selectedWard);
        }

        selectDistrict.appendChild(option);
      });
    })
    .catch(error => console.error('Lỗi khi gọi API: ', error));
}

function fetchWards(districtCode, selectedWard = '') {
  fetch(`${API_URL}/d/${districtCode}/?depth=2`)
    .then(response => response.json())
    .then(data => {
      const wards = data.wards;

      selectWard.innerHTML = `<option value="">Chọn phường/xã</option>`;

      wards.forEach(ward => {
        const option = document.createElement('option');
        option.value = ward.name;
        option.dataset.code = ward.code;
        option.textContent = ward.name;

        if (ward.name === selectedWard) {
          option.selected = true;
        }

        selectWard.appendChild(option);
      });
    })
    .catch(error => console.error('Lỗi khi gọi API: ', error));
}

// Thay đổi tỉnh thì load quận
selectProvince.addEventListener('change', () => {
  const provinceCode = selectProvince.selectedOptions[0]?.dataset.code;

  selectDistrict.innerHTML = `<option value="">Chọn quận/huyện</option>`;
  selectWard.innerHTML = `<option value="">Chọn phường/xã</option>`;

  if (!provinceCode) return;

  fetchDistricts(provinceCode);
});

// Thay đổi quận thì load phường
selectDistrict.addEventListener('change', () => {
  const districtCode = selectDistrict.selectedOptions[0]?.dataset.code;

  selectWard.innerHTML = `<option value="">Chọn phường/xã</option>`;

  if (!districtCode) return;

  fetchWards(districtCode);
});