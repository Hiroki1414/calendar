import React, { useState, useEffect } from 'react';

function MainApp({ fetchEvents, handleUpdate, handleEdit, handleDelete }) {
  const [events, setEvents] = useState([]);
  const [editingEvent, setEditingEvent] = useState(null);

  useEffect(() => {
    fetchEvents(setEvents);
  }, [fetchEvents]);

  return (
    <div>
      <h1>Events</h1>
      {events.map(event => (
        <div key={event.schedule_id}>
          {editingEvent && editingEvent.schedule_id === event.schedule_id ? (
            <div>
              <input
                type="text"
                name="task"
                value={editingEvent.task}
                onChange={(e) => handleEdit(e, editingEvent, setEditingEvent)}
              />
              <button onClick={() => handleUpdate(event.schedule_id, editingEvent, setEvents, setEditingEvent)}>Save</button>
            </div>
          ) : (
            <div>
              <span>{event.task}</span>
              <button onClick={() => handleEdit(event, setEditingEvent)}>Edit</button>
              <button onClick={() => handleDelete(event.schedule_id, setEvents)}>Delete</button>
            </div>
          )}
        </div>
      ))}
    </div>
  );
}

export default MainApp;
