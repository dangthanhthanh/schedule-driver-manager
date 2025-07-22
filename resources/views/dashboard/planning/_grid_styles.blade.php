<style>
 .planning-grid-wrapper {
  overflow-x: auto;
 }

 .planning-grid {
  min-width: 1200px;
  border-collapse: separate;
  border-spacing: 0;
  font-size: 0.85rem;
 }

 .planning-grid th,
 .planning-grid td {
  white-space: nowrap;
  padding: .25rem .5rem;
  border: 1px solid var(--bs-border-color);
 }

 .planning-grid thead th {
  text-align: center;
  vertical-align: middle;
 }

 .planning-grid .sticky-col {
  position: sticky;
  left: 0;
  background: var(--bs-body-bg);
  z-index: 2;
 }

 .planning-grid .sticky-col-2 {
  left: 60px;
  z-index: 2;
 }

 .planning-grid .sticky-col-3 {
  left: 220px;
  z-index: 2;
 }

 .planning-grid .sticky-col-4 {
  left: 420px;
  z-index: 2;
 }

 .planning-grid .sticky-col-5 {
  left: 600px;
  z-index: 2;
 }

 .planning-grid .cell-empty {
  background: var(--bs-secondary-bg);
  color: var(--bs-secondary-color);
  cursor: pointer;
  text-align: center;
 }

 .planning-grid .cell-has {
  cursor: pointer;
 }

 .planning-grid .cell-has .badge {
  display: inline-block;
  max-width: 110px;
  overflow: hidden;
  text-overflow: ellipsis;
 }

 @media (max-width:767.98px) {
  .planning-grid {
   font-size: 0.75rem;
  }
 }
</style>