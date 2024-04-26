import React from 'react';
import ReactDOM from 'react-dom/client';
import MainApp from "./components/mainapp";

document.addEventListener('DOMContentLoaded', () => {
  const root = ReactDOM.createRoot(document.getElementById('app'));
  root.render(<App />);
});

function App() {
  const fetchEvents = async (setEvents) => {
    try {
      const response = await fetch('http://localhost:8888/rest/calendar/list');
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const data = await response.json();
      if (Array.isArray(data)) {
        setEvents(data);
      } else {
          console.error('Data is not an array:', data);
      }
    } catch (error) {
        console.error("Fetching events failed:", error);
    }
  };

  const handleEdit = (event, setEditingEvent) => {
    setEditingEvent({ ...event });
  };

  const handleUpdate = async (id, editingEvent, setEvents, setEditingEvent) => {
    try {
      const response = await fetch(`http://localhost:8888/rest/calendar/update/${id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(editingEvent),
      });
      if (!response.ok) throw new Error('Network response was not ok.');
      setEvents(); 
      setEditingEvent(null); 
    } catch (error) {
      console.error("Updating event failed:", error);
    }
  };

  const handleDelete = async (id, setEvents) => {
    try {
      const response = await fetch(`http://localhost:8888/rest/calendar/delete/${id}`, {
        method: 'DELETE',
      });
      if (!response.ok) throw new Error('Network response was not ok.');
      setEvents(); 
    } catch (error) {
      console.error("Deleting event failed:", error);
    }
  };

  return <MainApp fetchEvents={fetchEvents} handleUpdate={handleUpdate} handleEdit={handleEdit} handleDelete={handleDelete} />;
}

export default App;
