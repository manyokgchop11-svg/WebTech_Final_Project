// Address Management JavaScript

function showAddAddressModal() {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-map-marker-alt"></i> Add New Address';
    document.getElementById('addressForm').reset();
    document.getElementById('address_id').value = '';
    document.getElementById('addressModal').style.display = 'block';
}

function closeAddressModal() {
    document.getElementById('addressModal').style.display = 'none';
}

async function editAddress(addressId) {
    try {
        const response = await fetch(`../api/get_address.php?id=${addressId}`);
        const result = await response.json();

        if (result.success) {
            const addr = result.address;
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit"></i> Edit Address';
            document.getElementById('address_id').value = addr.id;
            document.getElementById('label').value = addr.label;
            document.getElementById('address_line').value = addr.address_line;
            document.getElementById('city').value = addr.city;
            document.getElementById('state').value = addr.state;
            document.getElementById('phone').value = addr.phone;
            document.getElementById('is_default').checked = addr.is_default == 1;
            document.getElementById('addressModal').style.display = 'block';
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to load address');
    }
}

async function setDefault(addressId) {
    try {
        const response = await fetch('../api/set_default_address.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ address_id: addressId })
        });

        const result = await response.json();

        if (result.success) {
            location.reload();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to set default address');
    }
}

async function deleteAddress(addressId) {
    if (!confirm('Are you sure you want to delete this address?')) {
        return;
    }

    try {
        const response = await fetch('../api/delete_address.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ address_id: addressId })
        });

        const result = await response.json();

        if (result.success) {
            location.reload();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to delete address');
    }
}

// Handle form submission
document.getElementById('addressForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const addressData = {
        id: document.getElementById('address_id').value,
        label: document.getElementById('label').value,
        address_line: document.getElementById('address_line').value,
        city: document.getElementById('city').value,
        state: document.getElementById('state').value,
        phone: document.getElementById('phone').value,
        is_default: document.getElementById('is_default').checked ? 1 : 0
    };

    try {
        const response = await fetch('../api/save_address.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(addressData)
        });

        const result = await response.json();

        if (result.success) {
            alert('Address saved successfully');
            location.reload();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to save address');
    }
});

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('addressModal');
    if (event.target == modal) {
        closeAddressModal();
    }
}
