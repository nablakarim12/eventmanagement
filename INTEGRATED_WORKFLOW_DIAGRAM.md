# 🎯 NEW INTEGRATED WORKFLOW DIAGRAM

```
┌─────────────────────────────────────────────────────────────────────┐
│                    PRESENTATION APPROVAL WORKFLOW                    │
│                  (Integrated into Evaluation Results)                │
└─────────────────────────────────────────────────────────────────────┘

STEP 1: REVIEWERS SUBMIT SCORES
┌──────────────────────────────────────┐
│  👨‍🏫 Jury Member Login               │
│                                      │
│  ➜ Navigate to "Jury Mapping"       │
│  ➜ View assigned papers             │
│  ➜ Score using rubric (categories)  │
│  ➜ Submit evaluation                │
│                                      │
│  ✅ Scores saved to database         │
└──────────────────────────────────────┘
           ↓
┌──────────────────────────────────────────────────────────┐
│  📊 SCORES STORED IN:                                    │
│                                                          │
│  • rubric_item_scores table                             │
│    - evaluator_id (jury member)                         │
│    - event_paper_id (paper)                             │
│    - rubric_item_id (specific criterion)                │
│    - score (points given)                               │
│    - comment (optional feedback)                        │
└──────────────────────────────────────────────────────────┘
           ↓
           ↓
STEP 2: ORGANIZER VIEWS EVALUATION RESULTS
┌──────────────────────────────────────┐
│  🎯 Event Organizer Login            │
│                                      │
│  ➜ Click "Evaluation Results" 📊     │
│     (in sidebar)                     │
│                                      │
│  ➜ See list of events:              │
│     • Event Name                     │
│     • Papers Count                   │
│     • Evaluations Count              │
└──────────────────────────────────────┘
           ↓
┌──────────────────────────────────────────────────────────┐
│  📋 SELECT EVENT                                         │
│                                                          │
│  Click on: "APCET 2025"                                  │
│                                                          │
│  Shows papers list:                                      │
│  ┌────────────────────────────────────────────────────┐ │
│  │ # │ Author    │ Paper Title      │ Evaluations    │ │
│  ├───┼───────────┼──────────────────┼────────────────┤ │
│  │ 1 │ Aisyah    │ AI-Driven...    │ 2/2 ✅         │ │
│  │ 2 │ Luqmanul  │ EdTech...       │ 2/2 ✅         │ │
│  │ 3 │ Alvin     │ E-Learning...   │ 1/2 ⏳         │ │
│  │ 4 │ Kumar     │ Analytics...    │ 0/2 ❌         │ │
│  └────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────┘
           ↓
           ↓
STEP 3: VIEW PAPER DETAILS
┌──────────────────────────────────────────────────────────┐
│  📝 CLICK ON: "Aisyah's Paper"                           │
│                                                          │
│  Shows detailed evaluation page:                        │
│                                                          │
│  ╔═══════════════════════════════════════════════════╗  │
│  ║  📄 PAPER INFORMATION                             ║  │
│  ║  Title: AI-Driven Learning Analytics             ║  │
│  ║  Author: Aisyah Rahman (aisyah@example.com)     ║  │
│  ║  Paper ID: #123                                   ║  │
│  ║  Total Evaluators: 2 Jury Members                ║  │
│  ╚═══════════════════════════════════════════════════╝  │
│                                                          │
│  ╔═══════════════════════════════════════════════════╗  │
│  ║  👨‍🏫 REVIEWER 1: Dr. Ahmad                        ║  │
│  ║  Total Score: 90/100                              ║  │
│  ║  ─────────────────────────────────────────────    ║  │
│  ║  📚 Content Quality: 45/50                        ║  │
│  ║     • Relevance: 23/25 ✅                         ║  │
│  ║     • Originality: 22/25 ✅                       ║  │
│  ║  📊 Methodology: 30/30                            ║  │
│  ║     • Research Design: 15/15 ✅                   ║  │
│  ║     • Data Analysis: 15/15 ✅                     ║  │
│  ║  📝 Presentation: 15/20                           ║  │
│  ║     • Writing Quality: 8/10 ✅                    ║  │
│  ║     • Organization: 7/10 ✅                       ║  │
│  ╚═══════════════════════════════════════════════════╝  │
│                                                          │
│  ╔═══════════════════════════════════════════════════╗  │
│  ║  👩‍🏫 REVIEWER 2: Prof. Siti                       ║  │
│  ║  Total Score: 94/100                              ║  │
│  ║  ─────────────────────────────────────────────    ║  │
│  ║  📚 Content Quality: 48/50                        ║  │
│  ║     • Relevance: 24/25 ✅                         ║  │
│  ║     • Originality: 24/25 ✅                       ║  │
│  ║  📊 Methodology: 28/30                            ║  │
│  ║     • Research Design: 14/15 ✅                   ║  │
│  ║     • Data Analysis: 14/15 ✅                     ║  │
│  ║  📝 Presentation: 18/20                           ║  │
│  ║     • Writing Quality: 9/10 ✅                    ║  │
│  ║     • Organization: 9/10 ✅                       ║  │
│  ╚═══════════════════════════════════════════════════╝  │
│                                                          │
│  ╔═══════════════════════════════════════════════════╗  │
│  ║  📊 EVALUATION SUMMARY                            ║  │
│  ║  ┌────────────┬──────────────┬───────────────┐   ║  │
│  ║  │ Evaluators │ Average Score│ Score Range   │   ║  │
│  ║  ├────────────┼──────────────┼───────────────┤   ║  │
│  ║  │     2      │    92.0      │  90.0 - 94.0  │   ║  │
│  ║  └────────────┴──────────────┴───────────────┘   ║  │
│  ╚═══════════════════════════════════════════════════╝  │
└──────────────────────────────────────────────────────────┘
           ↓
           ↓ SCROLL DOWN ↓
           ↓
STEP 4: MAKE PRESENTATION DECISION
┌──────────────────────────────────────────────────────────┐
│  🎯 PRESENTATION DECISION SECTION                        │
│     (Bottom of same page!)                               │
│                                                          │
│  ┌────────────────────────────────────────────────────┐ │
│  │ ⏳ Awaiting Decision                                │ │
│  ├────────────────────────────────────────────────────┤ │
│  │                                                    │ │
│  │  [✅ Approve for Presentation]  [❌ Reject]        │ │
│  │      (Green Button)              (Red Button)     │ │
│  └────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────┘
           ↓                              ↓
           ↓                              ↓
    OPTION A: APPROVE             OPTION B: REJECT
           ↓                              ↓
┌──────────────────────────────┐  ┌─────────────────────────┐
│  ✅ APPROVE MODAL OPENS      │  │  ❌ REJECT MODAL OPENS  │
│                              │  │                         │
│  "Aisyah scored 92.0"        │  │  "Aisyah scored 92.0"   │
│                              │  │                         │
│  Fields:                     │  │  Field:                 │
│  • Queue #: [1]              │  │  • Reason: [Required]   │
│  • Time: [Jan 15, 10:00 AM]  │  │    "Thank you for your  │
│  • Link: [zoom.us/j/123...]  │  │     submission..."      │
│  • Location: [Room 301]      │  │                         │
│                              │  │  [✗ Reject & Notify]    │
│  [✓ Approve & Notify]        │  │                         │
└──────────────────────────────┘  └─────────────────────────┘
           ↓                              ↓
           ↓                              ↓
STEP 5: SYSTEM PROCESSES
┌──────────────────────────────┐  ┌─────────────────────────┐
│  ✅ APPROVAL PROCESS         │  │  ❌ REJECTION PROCESS   │
│                              │  │                         │
│  1. Update Registration:     │  │  1. Update Registration:│
│     • status = 'selected'    │  │     • status = 'rejected│
│     • queue = 1              │  │     • rejection_reason  │
│     • time = Jan 15 10:00    │  │     • average_score=92.0│
│     • link = zoom.us...      │  │     • notified_at=now() │
│     • location = Room 301    │  │                         │
│     • average_score = 92.0   │  │  2. Send Notifications: │
│     • notified_at = now()    │  │     ✉️ Email sent       │
│                              │  │     🔔 Dashboard notify │
│  2. Send Notifications:      │  │                         │
│     ✉️ Email sent            │  │  3. Return Success:     │
│     🔔 Dashboard notification│  │     "Participant        │
│                              │  │      notified"          │
│  3. Return Success:          │  │                         │
│     "Participant approved!"  │  │                         │
└──────────────────────────────┘  └─────────────────────────┘
           ↓                              ↓
           ↓                              ↓
STEP 6: PARTICIPANT RECEIVES NOTIFICATION
┌──────────────────────────────┐  ┌─────────────────────────┐
│  ✅ APPROVED NOTIFICATION    │  │  ❌ REJECTED NOTIF      │
│                              │  │                         │
│  📧 EMAIL:                   │  │  📧 EMAIL:              │
│  Subject: 🎉 Congratulations!│  │  Subject: Review Results│
│                              │  │                         │
│  Body:                       │  │  Body:                  │
│  "You're selected for        │  │  "Thank you for your    │
│   presentation at APCET 2025"│  │   submission. After     │
│                              │  │   review, we regret..." │
│  • Your score: 92.0          │  │                         │
│  • Queue: #1                 │  │  • Your score: 92.0     │
│  • Time: Jan 15, 10:00 AM    │  │  • Feedback: [reason]   │
│  • Zoom: zoom.us/j/123...    │  │                         │
│  • Room: 301                 │  │  "We encourage future   │
│                              │  │   submissions"          │
│  [View Dashboard]            │  │                         │
│                              │  │  [View Dashboard]       │
│  ─────────────────────       │  │  ─────────────────      │
│  🔔 DASHBOARD:               │  │  🔔 DASHBOARD:          │
│  • Shows same info           │  │  • Shows same info      │
│  • Notification badge        │  │  • Notification badge   │
└──────────────────────────────┘  └─────────────────────────┘
           ↓                              ↓
           ↓                              ↓
STEP 7: PAGE UPDATES AUTOMATICALLY
┌──────────────────────────────┐  ┌─────────────────────────┐
│  ✅ SHOWS APPROVED CARD      │  │  ❌ SHOWS REJECTED CARD │
│                              │  │                         │
│  ╔════════════════════════╗  │  │  ╔═══════════════════╗  │
│  ║ ✓ Approved for         ║  │  │  ║ ✗ Not Selected    ║  │
│  ║   Presentation         ║  │  │  ║                   ║  │
│  ║                        ║  │  │  ║ Reason: [...]     ║  │
│  ║ Queue: #1              ║  │  │  ╚═══════════════════╝  │
│  ║ Time: Jan 15, 10:00 AM ║  │  │                         │
│  ║ Zoom: zoom.us/j/123... ║  │  │  (Can't re-reject)      │
│  ║ Room: 301              ║  │  │                         │
│  ╚════════════════════════╝  │  │                         │
│                              │  │                         │
│  (Can't re-approve)          │  │                         │
└──────────────────────────────┘  └─────────────────────────┘

═══════════════════════════════════════════════════════════

KEY FEATURES:
✅ All in ONE PAGE - no separate navigation
✅ See scores & make decision together
✅ Automatic email + dashboard notifications
✅ Status persistence (can't duplicate)
✅ Delivery mode aware (link/location based on event type)

═══════════════════════════════════════════════════════════

NAVIGATION PATH:
Evaluation Results → Select Event → Click Paper → Approve/Reject
(4 clicks total!)

═══════════════════════════════════════════════════════════
```
