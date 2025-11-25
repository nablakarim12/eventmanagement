## QR ATTENDANCE SYSTEM - TECHNICAL FLOW DIAGRAM

### ORGANIZER WORKFLOW:
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Create Event  │───▶│  Auto-Generate  │───▶│   Display QR    │
│                 │    │   QR Codes      │    │   Codes Page    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
        │                        │                        │
        ▼                        ▼                        ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│ Event Created   │    │ 2 QR Codes:     │    │ Print/Share QR  │
│ in Database     │    │ - check_in      │    │ Codes for Event │
│                 │    │ - check_out     │    │                 │
└─────────────────┘    └─────────────────┘    └─────────────────┘

### PARTICIPANT WORKFLOW:
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Scan QR Code  │───▶│  Open Web Page  │───▶│  Fill Attendance│
│   (Any Device)  │    │  in Browser     │    │     Form        │
└─────────────────┘    └─────────────────┘    └─────────────────┘
        │                        │                        │
        ▼                        ▼                        ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│ QR Contains     │    │ Show Event Info │    │ Submit Form     │
│ Scan URL        │    │ & Role Selection│    │ Process         │
│                 │    │                 │    │ Attendance      │
└─────────────────┘    └─────────────────┘    └─────────────────┘

### DATABASE PROCESSING:
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│ Validate QR     │───▶│ Check User      │───▶│ Record          │
│ Code & Event    │    │ Registration    │    │ Attendance      │
└─────────────────┘    └─────────────────┘    └─────────────────┘
        │                        │                        │
        ▼                        ▼                        ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│ QR Valid?       │    │ Auto-Register   │    │ Timestamp +     │
│ Event Active?   │    │ if Not Already  │    │ Role Saved      │
│ Time Valid?     │    │ Registered      │    │                 │
└─────────────────┘    └─────────────────┘    └─────────────────┘

### FINAL RESULT:
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│ Attendance      │───▶│ Visible in      │───▶│ Reports &       │
│ Automatically   │    │ Organizer       │    │ Analytics       │
│ Recorded        │    │ Dashboard       │    │ Available       │
└─────────────────┘    └─────────────────┘    └─────────────────┘