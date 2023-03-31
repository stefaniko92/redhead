PHP Developer Task
Your task is to write an API (using Laravel framework) based on a given database model:
Database Model
At least, you should have the following things implemented:
1. Make an authentication system using Sanctum.
2. Make CRUD API for approvers.
   a. Approvers are users of the system with the type “APPROVER”.
3. Make CRUD API for employees (professors and traders).
   a. This API should also create the corresponding user model with the
   “NON_APPROVER” type.
   b. Employees have the number of hours that they’re available to work every day.
   (professors:total_available_hours, traders:working_hours)
4. Make API routes for jobs.
   a. Jobs are made only for one day in the month and they can not exceed the total
   number of hours that the employee is available to work.
   b. Create CRUD for jobs, and be aware that every employee can work a certain
   number of hours.
5. Make API routes for job approvals.
   a. Create an approving route, and be aware that only approvers can approve.
6. Make an API report that will return the statistics of how much employees earned every
   month per year including only totally approved jobs.
   a. This API route should include managers' and workers' total earnings.
   b. Job is approved when all approver votes are ‘APPROVED’.
   c. The first year is when the first job was made and the last year is when the last job
   was made.
   d. Consider a minimum of 2000 working days per employee. (This means that the
   route has to be a high-performance route.)
7. Ensure every API route is well-tested, you need to cover routes with feature tests.
