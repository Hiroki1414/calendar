import React, { useState, useEffect } from 'react';
import '../styles/liststyle.css';

function MainApp({ fetchEvents, handleUpdate, handleEdit, handleDelete }) {
  const [events, setEvents] = useState([]);
  const [editingEvent, setEditingEvent] = useState(null);
  const [colorOptions, setColorOptions] = useState({});

  useEffect(() => {
    const fetchColors = async () => {
      try {
        const response = await fetch('http://localhost:8888/rest/calendar/colors');
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        const data = await response.json();
        if (data.status === 'success') {
          setColorOptions(data.data);  // data.data は色情報の配列
        } else {
          console.error('Failed to fetch colors:', data);
        }
      } catch (error) {
        console.error('Error fetching colors:', error);
      }
    };

    fetchColors();
    fetchEvents(setEvents);
  }, [fetchEvents]);

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setEditingEvent(prev => ({ ...prev, [name]: value }));
  };

  const handleCancel = () => {
    setEditingEvent(null); 
  };

  const formatTime = (isoString) => {
    let localDate = new Date(isoString);
    let year = localDate.getFullYear();
    let month = ('0' + (localDate.getMonth() + 1)).slice(-2);
    let day = ('0' + localDate.getDate()).slice(-2);
    let hours = ('0' + localDate.getHours()).slice(-2);
    let minutes = ('0' + localDate.getMinutes()).slice(-2);
    return `${year}-${month}-${day} ${hours}:${minutes}`;
  };

  return (
    <div>
      <h1>Events</h1>
      {events.map(event => (
        <div key={event.schedule_id} className="card" style={{ borderColor: colorOptions[event.color]?.color }}>
          {editingEvent && editingEvent.schedule_id === event.schedule_id ? (
            <div>
              <input type="text" name="title" className="input" placeholder="Event Title" value={editingEvent.title} onChange={handleInputChange} />
              <input type="datetime-local" name="start" className="input" value={editingEvent.start.slice(0, 16)} onChange={handleInputChange} />
              <input type="datetime-local" name="end" className="input" value={editingEvent.end.slice(0, 16)} onChange={handleInputChange} />
              <select name="color" className="input" value={editingEvent.color} onChange={handleInputChange}>
                {Object.keys(colorOptions).map(key => (
                  <option key={key} value={key}>{colorOptions[key].name}</option>
                ))}
              </select>
              <div className="button-group">
                <button className="button" onClick={() => handleUpdate(event.schedule_id, editingEvent, setEvents, setEditingEvent)}>Save</button>
                <button className="button cancel-button" onClick={handleCancel}>Cancel</button>
              </div>
            </div>
          ) : (
            <div>
              <p className="event-info"><strong>Title:</strong> {event.title}</p>
              <p className="event-info"><strong>Start:</strong> {formatTime(event.start)}</p>
              <p className="event-info"><strong>End:</strong> {formatTime(event.end)}</p>
              <div className="button-group">
                <button className="button" onClick={() => handleEdit(event, setEditingEvent)}>Edit</button>
                <button className="button" onClick={() => handleDelete(event.schedule_id, setEvents)}>Delete</button>
              </div>
            </div>
          )}
        </div>
      ))}
    </div>
  );
}

export default MainApp;
