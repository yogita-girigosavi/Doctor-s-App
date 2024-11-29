let selectedClinicID = null;
let selectedDoctorID = null;
let selectedDate = null;

function fetchSlots() {
    const date = document.getElementById('appointment-date').value;
    const slotsContainer = document.getElementById('slots-container');
    if (!date) {
        slotsContainer.innerHTML = 'Please select a date';
        return;
    }

    // Log selected clinic ID and doctor ID
    console.log("Selected Clinic ID:", selectedClinicID);
    console.log("Selected Doctor ID:", selectedDoctorID);
    console.log("Selected Date:", date);

    fetch(`fetch_slots.php?date=${date}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            slotsContainer.innerHTML = '';
            if (data.length === 0) {
                slotsContainer.innerHTML = 'No Slots Available';
                return;
            }

            data.forEach(slot => {
                const slotDiv = document.createElement('div');
                slotDiv.className = `slot ${slot.is_booked ? 'booked' : 'available'}`;
                slotDiv.textContent = slot.time_slot; // Display the time slot

                if (!slot.is_booked) {
                    slotDiv.addEventListener('click', () => bookSlot(slot.id)); // Use slot.id
                }

                slotsContainer.appendChild(slotDiv);
            });
        })
        .catch(error => {
            slotsContainer.innerHTML = 'Error fetching slots';
            console.error('Fetch error:', error);
        });
}

function bookSlot(slotID) {
    // Log the booking details
    console.log("Booking Slot ID:", slotID);
    console.log("Selected Clinic ID:", selectedClinicID);
    console.log("Selected Doctor ID:", selectedDoctorID);
    console.log("Selected Date:", selectedDate);

    // Send booking request to server
    fetch('book_slot.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            id: slotID,
            clinic_id: selectedClinicID, // Include selected clinic ID
            doctor_id: selectedDoctorID,   // Include selected doctor ID
            appointment_date: selectedDate  // Include selected date
        })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        fetchSlots(); // Refresh slots after booking
    })
    .catch(error => {
        alert('Error booking slot');
        console.error(error);
    });
}

// Call this function when fetching doctor details
function fetchDoctorDetails() {
    console.log("Fetching doctor details...");
    fetch('fetch_doctor.php')
        .then(response => response.json())
        .then(data => {
            console.log("Doctor data received:", data); // Log the doctor data
            if (data && Object.keys(data).length > 0) {
                selectedDoctorID = data.id; // Store selected doctor ID
                document.getElementById('doctor-name').innerText = data.name;
                document.getElementById('doctor-qualifications').innerText = data.qualification;
                document.getElementById('doctor-specialty').innerText = data.specialty;
                fetchFeeDetails(); // Call to fetch fees
            } else {
                console.error('No doctor found');
            }
        })
        .catch(error => console.error('Error fetching doctor details:', error));
}

function fetchClinics() {
    fetch('fetch_clinics.php')
        .then(response => response.json())
        .then(data => {
            const clinicSelect = document.getElementById('clinic');
            clinicSelect.innerHTML = ''; // Clear existing options

            data.forEach(clinic => {
                const option = document.createElement('option');
                option.value = clinic.id;
                option.textContent = clinic.name;
                clinicSelect.appendChild(option);
            });

            // Trigger clinic details fetch for the first clinic
            if (data.length > 0) {
                selectedClinicID = data[0].id; // Store the first clinic ID
                fetchClinicDetails();
            }
        })
        .catch(error => console.error('Error fetching clinics:', error));
}

function fetchClinicDetails() {
    const clinicID = document.getElementById('clinic').value;
    selectedClinicID = clinicID; // Store selected clinic ID
    fetch(`fetch_clinic_details.php?clinicID=${clinicID}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('clinic-address').textContent = data.address || 'Address not available';
        })
        .catch(error => console.error('Error fetching clinic details:', error));
}

document.getElementById('appointment-date').addEventListener('change', function () {
    selectedDate = this.value; // Store selected date
    console.log("Selected Date:", selectedDate); // Log selected date
    fetchSlots(); // Fetch available slots for the selected date
});

function fetchDoctorDetails() {
    console.log("Fetching doctor details...");
    fetch('fetch_doctor.php')
        .then(response => response.json())
        .then(data => {
            console.log("Doctor data received:", data); // Log the doctor data
            if (data && Object.keys(data).length > 0) {
                selectedDoctorID = data.id; // Store selected doctor ID
                document.getElementById('doctor-name').innerText = data.name;
                document.getElementById('doctor-qualifications').innerText = data.qualification;
                document.getElementById('doctor-specialty').innerText = data.specialty;
                fetchFeeDetails(); // Call to fetch fees
            } else {
                console.error('No doctor found');
            }
        })
        .catch(error => console.error('Error fetching doctor details:', error));
}

function fetchFeeDetails() {
    console.log("Fetching fee details...");
    fetch('fetch_fee.php')
        .then(response => response.json())
        .then(data => {
            console.log("Fee data received:", data); // Log the fee data
            if (data && Object.keys(data).length > 0) {
                document.getElementById('doctor-fees').innerText = `First Visit Fees: ₹${data.first_visit_fee} | Follow-Up Fees: ₹${data.follow_up_fee}`;
            } else {
                console.error('No fee details found');
            }
        })
        .catch(error => console.error('Error fetching fee details:', error));
}

// Initialize clinics on page load
document.addEventListener('DOMContentLoaded', fetchClinics);

document.addEventListener('DOMContentLoaded', () => {
    fetchClinics();
    fetchFeeDetails();
    fetchDoctorDetails();
});