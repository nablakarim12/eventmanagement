# QUICK REFERENCE CARD

```
╔═══════════════════════════════════════════════════════════════╗
║                    EVENT TYPE QUICK GUIDE                     ║
╠═══════════════════════════════════════════════════════════════╣
║                                                               ║
║  INNOVATION COMPETITION          ACADEMIC CONFERENCE          ║
║  ═══════════════════════         ════════════════════         ║
║                                                               ║
║  Roles:                          Roles:                       ║
║  • participant                   • participant                ║
║  • jury                          • reviewer                   ║
║  • both                          • both                       ║
║                                                               ║
║  Paper Submission:               Paper Submission:            ║
║  ❌ NO                            ✅ YES (Required)            ║
║                                                               ║
║  How it works:                   How it works:                ║
║  • Present ideas live            • Submit paper PDF           ║
║  • Jury judges at event          • Reviewers review before    ║
║  • No papers needed              • Present at conference      ║
║                                                               ║
║  Forms to Build:                 Forms to Build:              ║
║  ✅ Registration                  ✅ Registration              ║
║  ❌ Paper Submission              ✅ Paper Submission          ║
║                                                               ║
╠═══════════════════════════════════════════════════════════════╣
║                     ROLE TERMINOLOGY                          ║
╠═══════════════════════════════════════════════════════════════╣
║                                                               ║
║  Database Field: role (VARCHAR)                               ║
║  ────────────────────────────────                             ║
║                                                               ║
║  Innovation Values:    Conference Values:                     ║
║  • 'participant'       • 'participant'                        ║
║  • 'jury'              • 'reviewer'                           ║
║  • 'both'              • 'both'                               ║
║                                                               ║
║  Qualification Fields (SAME for both):                        ║
║  • jury_qualification_summary                                 ║
║  • jury_institution                                           ║
║  • jury_position                                              ║
║  • jury_years_experience                                      ║
║  • jury_expertise_areas                                       ║
║  • jury_experience                                            ║
║  • jury_qualification_documents                               ║
║                                                               ║
║  Note: Field names say "jury" but used for both jury          ║
║  (innovation) and reviewer (conference) roles!                ║
║                                                               ║
╠═══════════════════════════════════════════════════════════════╣
║                   CONDITIONAL LOGIC                           ║
╠═══════════════════════════════════════════════════════════════╣
║                                                               ║
║  Show Qualification Fields:                                   ║
║  ─────────────────────────                                    ║
║  IF role IN ('jury', 'reviewer', 'both')                      ║
║  THEN show jury qualification fields                          ║
║                                                               ║
║  Show Paper Submission:                                       ║
║  ──────────────────────                                       ║
║  IF event.category.name === 'Academic Conference'             ║
║  AND role IN ('participant', 'both')                          ║
║  AND approval_status === 'approved'                           ║
║  THEN show paper submission form                              ║
║                                                               ║
║  Show Review Dashboard:                                       ║
║  ──────────────────────                                       ║
║  IF role IN ('jury', 'reviewer', 'both')                      ║
║  AND checked_in_at IS NOT NULL                                ║
║  AND approval_status === 'approved'                           ║
║  THEN show review/judging interface                           ║
║                                                               ║
╠═══════════════════════════════════════════════════════════════╣
║                    VALIDATION RULES                           ║
╠═══════════════════════════════════════════════════════════════╣
║                                                               ║
║  Registration Form:                                           ║
║  • role: required                                             ║
║  • jury_qualification_summary: required if jury/reviewer/both ║
║    (min 100, max 1000 chars)                                  ║
║  • jury_institution: required if jury/reviewer/both           ║
║  • jury_position: required if jury/reviewer/both              ║
║  • jury_years_experience: required if jury/reviewer/both      ║
║  • jury_expertise_areas: required if jury/reviewer/both       ║
║                                                               ║
║  Paper Submission Form (Academic Conference only):            ║
║  • title: required, min 10, max 255                           ║
║  • abstract: required, min 200 words                          ║
║  • keywords: required, min 3 keywords                         ║
║  • paper_file: required, PDF only, max 10MB                   ║
║  • authors: min 1 author required                             ║
║  • corresponding_author: exactly 1 required                   ║
║                                                               ║
╠═══════════════════════════════════════════════════════════════╣
║                  DATABASE TABLES USED                         ║
╠═══════════════════════════════════════════════════════════════╣
║                                                               ║
║  Innovation Competition:         Academic Conference:         ║
║  ✅ events                        ✅ events                    ║
║  ✅ event_registrations           ✅ event_registrations       ║
║  ❌ paper_submissions             ✅ paper_submissions         ║
║  ❌ paper_authors                 ✅ paper_authors             ║
║  ✅ jury_assignments              ✅ jury_assignments          ║
║  ✅ paper_reviews                 ✅ paper_reviews             ║
║                                                               ║
╠═══════════════════════════════════════════════════════════════╣
║                    CODE SNIPPET                               ║
╠═══════════════════════════════════════════════════════════════╣
║                                                               ║
║  // Check if event has paper submission                       ║
║  if ($event->category->name === 'Academic Conference') {      ║
║      // Show paper submission features                        ║
║  } else {                                                     ║
║      // Hide paper submission features                        ║
║  }                                                            ║
║                                                               ║
║  // Dynamic role options                                      ║
║  if ($event->category->name === 'Innovation Competition') {   ║
║      $evaluatorRole = 'jury';                                 ║
║      $evaluatorLabel = 'Jury (Judge Presentations)';          ║
║  } else {                                                     ║
║      $evaluatorRole = 'reviewer';                             ║
║      $evaluatorLabel = 'Reviewer (Review Papers)';            ║
║  }                                                            ║
║                                                               ║
║  // Check qualification fields needed                         ║
║  if (in_array($role, ['jury', 'reviewer', 'both'])) {         ║
║      // Require qualification fields                          ║
║  }                                                            ║
║                                                               ║
╚═══════════════════════════════════════════════════════════════╝

┌───────────────────────────────────────────────────────────────┐
│                    WORKFLOW SUMMARY                           │
└───────────────────────────────────────────────────────────────┘

INNOVATION COMPETITION:
1. User registers (participant/jury/both)
2. Organizer approves → QR generated
3. [NO PAPER SUBMISSION PHASE]
4. Event day: Check-in via QR/manual
5. Participants present ideas LIVE
6. Jury judges during event
7. Winners announced

ACADEMIC CONFERENCE:
1. User registers (participant/reviewer/both)
2. Organizer approves → QR generated
3. ✅ PAPER SUBMISSION PHASE ✅
4. Participants submit papers (PDFs)
5. Reviewers check-in (enables review dashboard)
6. Reviewers review papers BEFORE event
7. Conference day: Accepted papers presented

┌───────────────────────────────────────────────────────────────┐
│                   TESTING CHECKLIST                           │
└───────────────────────────────────────────────────────────────┘

Innovation Competition:
[ ] Registration shows "Jury" role
[ ] Qualification fields for jury
[ ] NO paper submission anywhere
[ ] NO "Submit Paper" button
[ ] Review dashboard says "judge at event"

Academic Conference:
[ ] Registration shows "Reviewer" role
[ ] Qualification fields for reviewer
[ ] Paper submission form available
[ ] "Submit Paper" button visible
[ ] Review dashboard shows papers
[ ] Can submit reviews before conference

Both Event Types:
[ ] QR code displays
[ ] Check-in works
[ ] Approval workflow works
[ ] Role badges correct
[ ] Correct terminology used
