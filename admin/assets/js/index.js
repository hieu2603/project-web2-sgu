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
      const selected = (province.name === selectedProvince) ? 'selected' : '';
      selectProvince.innerHTML += `
        <option value="${province.name}" data-code="${province.code}" ${selected}>
          ${province.name}
        </option>
      `;
    });

    // Nếu có tỉnh đã chọn thì gọi fetchDistricts
    const selectedOption = [...selectProvince.options].find(opt => opt.selected);
    if (selectedOption && selectedOption.dataset.code) {
      fetchDistricts(selectedOption.dataset.code);
    }
  })
  .catch(error => console.error('Lỗi khi gọi API: ', error));

// Lấy danh sách quận/huyện
function fetchDistricts(provinceCode) {
  fetch(`${API_URL}/p/${provinceCode}/?depth=2`)
    .then(response => response.json())
    .then(data => {
      const districts = data.districts;

      selectDistrict.innerHTML = `<option value="">Chọn quận/huyện</option>`;

      districts.forEach(district => {
        const selected = (district.name === selectedDistrict) ? 'selected' : '';
        selectDistrict.innerHTML += `
          <option value="${district.name}" data-code="${district.code}" ${selected}>
            ${district.name}
          </option>
        `;
      });

      const selectedOption = [...selectDistrict.options].find(opt => opt.selected);
      if (selectedOption && selectedOption.dataset.code) {
        fetchWards(selectedOption.dataset.code);
      }
    })
    .catch(error => console.error('Lỗi khi gọi API: ', error));
}

// Lấy danh sách phường/xã
function fetchWards(districtCode) {
  fetch(`${API_URL}/d/${districtCode}/?depth=2`)
    .then(response => response.json())
    .then(data => {
      const wards = data.wards;

      selectWard.innerHTML = `<option value="">Chọn phường/xã</option>`;

      wards.forEach(ward => {
        const selected = (ward.name === selectedWard) ? 'selected' : '';
        selectWard.innerHTML += `
          <option value="${ward.name}" data-code="${ward.code}" ${selected}>
            ${ward.name}
          </option>
        `;
      });
    })
    .catch(error => console.error('Lỗi khi gọi API: ', error));
}

selectProvince.addEventListener('change', event => {
  const provinceCode = selectProvince.selectedOptions[0].dataset.code;

  selectDistrict.innerHTML = `<option value="">Chọn quận/huyện</option>`;
  selectWard.innerHTML = `<option value="">Chọn phường/xã</option>`;

  if (!provinceCode) return;

  fetchDistricts(provinceCode);
});

selectDistrict.addEventListener('change', event => {
  const districtCode = selectDistrict.selectedOptions[0].dataset.code;

  if (!districtCode) {
    selectWard.innerHTML = `<option value="">Chọn phường/xã</option>`;
    return;
  }

  fetchWards(districtCode);
});
