import React, { useState, useEffect } from 'react';
import Select from 'react-select';
import { Container, Row, Col, Button } from 'react-bootstrap';
import '../UserProfile.css';
import { useAuth } from "../contexts/AuthContext";
import useCustomization from "../hooks/useCustomization";
import axios from "axios";
import Loading from "./Loading";

const UserProfile = () => {
	const { loginStorageData } = useAuth();
	const { sources, authors, categories } = useCustomization();
	const [loading, setLoading] = useState(false);
	const userId = loginStorageData.user.id;

	const [selectedSources, setSelectedSources] = useState([]);
	const [selectedCategories, setSelectedCategories] = useState([]);
	const [selectedAuthors, setSelectedAuthors] = useState([]);
	const [showSuccessMessage, setShowSuccessMessage] = useState(false);

	// useEffect(() => {
	// 	setLoading(true);
	// 	axios.get(`http://127.0.0.1:8000/api/checkSetting?user=${userId}`)
	// 		.then(response => {
	// 			setSelectedSources(response.data.sources.map(source => ({ value: source.api, label: source.name })));
	// 			setSelectedCategories(response.data.categories.map(category => ({ value: category.id, label: category.name })));
	// 			setSelectedAuthors(response.data.authors.map(author => ({ value: author.id, label: author.name })));
	// 		})
	// 		.catch(error => {
	// 			console.error("API Error:", error);
	// 		})
	// 		.finally(() => setLoading(false));
	// }, [userId]);

	useEffect(() => {
		setLoading(true);
		axios.get(`http://127.0.0.1:8000/api/checkSetting?user=${userId}`, {
			headers: {
				Authorization: `Bearer ${loginStorageData.token}` // Add the Authorization header
			}
		})
			.then(response => {
				const sourceOptions = sources?.map(source => ({ value: source.id, label: source.name }));
				const categoryOptions = categories?.map(category => ({ value: category.id, label: category.name }));

				const mappedSources = response.data.sources.map(sourceName => {
					return sourceOptions.find(option => option.label === sourceName);
				}).filter(Boolean);

				const mappedCategories = response.data.categories.map(categoryName => {
					return categoryOptions.find(option => option.label === categoryName);
				}).filter(Boolean);

				const mappedAuthors = response.data.authors.map(authorObj => {
					return { value: authorObj.name, label: authorObj.name };
				});;

				setSelectedSources(mappedSources);
				setSelectedCategories(mappedCategories);
				setSelectedAuthors(mappedAuthors);
			})
			.catch(error => {
				console.error("API Error:", error);
			})
			.finally(() => setLoading(false));
	}, [userId, sources, categories, authors]);




	const handleSubmit = async () => {
		const formatSelection = (selectedItems) => selectedItems.map(item => item.value);
		const payload = {
			user_id: userId,
			sources: formatSelection(selectedSources),
			categories: formatSelection(selectedCategories),
			authors: formatSelection(selectedAuthors),
		};

		try {
			const response = await axios.post('http://127.0.0.1:8000/api/storeSetting', payload, {
				headers: {
					Authorization: `Bearer ${loginStorageData.token}` // Add the Authorization header
				}
			});
			console.log('Settings updated:', response.data);
			setShowSuccessMessage(true);
			setTimeout(() => setShowSuccessMessage(false), 3000); // Hide the message after 3 seconds
		} catch (error) {
			console.error('API Error:', error.response);
			setShowSuccessMessage(false);
		}
	};


	const handleSourcesChange = selectedOptions => setSelectedSources(selectedOptions);
	const handleCategoriesChange = selectedOptions => setSelectedCategories(selectedOptions);
	const handleAuthorsChange = selectedOptions => setSelectedAuthors(selectedOptions);

	return (
		<Container className="user-profile">
			{loading && <Loading />}
			<Row>
				<Col>
					<h1 className="user-name">Welcome, {loginStorageData.user.name}</h1>
					<h3 className="user-name">Please Select your preferred news feed</h3>
				</Col>
			</Row>
			<Row>
				{showSuccessMessage && (
					<Col className="text-center">
						<div className="alert alert-success" role="alert">
							News Feed Settings updated successfully!
						</div>
					</Col>
				)}
			</Row>

			<Row>
				<Col md={4} className="sources-section">
					<h4>Main Sources</h4>
					<Select
						isMulti
						name="sources"
						options={sources?.map(source => ({ value: source.id, label: source.name }))}
						value={selectedSources}
						className="basic-multi-select"
						classNamePrefix="select"
						onChange={handleSourcesChange}
					/>
				</Col>
				<Col md={4} className="categories-section">
					<h4>Categories</h4>
					<Select
						isMulti
						name="categories"
						options={categories?.map(category => ({ value: category.id, label: category.name }))}
						value={selectedCategories}
						className="basic-multi-select"
						classNamePrefix="select"
						onChange={handleCategoriesChange}
					/>
				</Col>
				<Col md={4} className="authors-section">
					<h4>Authors</h4>
					<Select
						isMulti
						name="authors"
						options={authors?.map(author => ({ value: author.author, label: author.author }))}
						value={selectedAuthors}
						className="basic-multi-select"
						classNamePrefix="select"
						onChange={handleAuthorsChange}
					/>
				</Col>
			</Row>
			<Row>
				<Col className="text-center mt-4">
					<Button variant="primary" onClick={handleSubmit}>Submit Preferences</Button>
				</Col>
			</Row>
		</Container>
	);
};

export default UserProfile;
