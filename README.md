# FGCK Joyland Pastor Appointment System

## Requirements
- XAMPP with Apache, PHP 8.1+ and MySQL/MariaDB
- Project folder: `C:\xampp\htdocs\fgck_joyland`

## Fresh installation
1. Copy `fgck_joyland` into `C:\xampp\htdocs\`.
2. Start Apache and MySQL in XAMPP.
3. Open phpMyAdmin and import `database/schema.sql`.
4. Open `http://localhost/fgck_joyland/`.
5. Development pastor login: **pastor / password**. Change this password before production use.

## Existing installation upgrade
If you already imported an earlier version, import `database/migrate_existing.sql` after the existing schema. This is important because older versions made `appointments.slot_id` permanently unique, which prevented a cancelled slot from being booked again.

## Member workflow
Register -> automatic secure login -> dashboard -> choose a Wednesday -> select a 30-minute available slot -> enter purpose -> submit -> receive appointment reference -> pastor confirms/declines -> member sees updated status.

## Speed and reliability
- Wednesday slots are created automatically when a booking date is opened, so the system does not run out of future slots.
- Booking uses a database transaction and row lock on the selected slot, preventing two members from successfully booking the same slot at the same time.
- Active-slot and member-status indexes are included for fast lookups.
- Login and registration regenerate the session ID.
- CSRF protection is enabled on state-changing forms.
- Passwords are stored with `password_hash()` and checked with `password_verify()`.
- Cancelled slots can be booked again while the appointment history remains available.

## Important
This package has been syntax-checked with PHP. A live end-to-end database test still needs to be performed in your XAMPP/MySQL environment because this execution environment does not provide a running MySQL/MariaDB server.


## Pastor live availability and appointment time controls

- Set **Office status = Unavailable** in Staff > Settings. Member booking immediately shows **Pastor's Office is Closed** and all slots are non-bookable.
- Staff can close individual slots in Staff > Slot Availability. Members see those slots as **Closed** automatically.
- Staff can use **Adjust Time** on pending/confirmed appointments to shorten or extend an individual member's appointment from 5–120 minutes, subject to office hours and overlap protection.
- The member dashboard and My Appointments pages automatically refresh when an appointment time changes.
- The member Book Appointment page checks availability every 5 seconds, so office/slot changes are reflected without a manual refresh.

## Pastor appointment time adjustment
The Pastor can use Staff > Appointments > Adjust Time to shorten, extend, or move an active appointment. Adjustments are stored directly on the appointment (`adjusted_start_time` / `adjusted_end_time`) so the original availability slot remains intact. Members see the effective adjusted time on Dashboard and My Appointments, and the portal detects appointment changes automatically through the live availability signature.

For existing installations, run `database/migrate_existing.sql` once after updating the files.
